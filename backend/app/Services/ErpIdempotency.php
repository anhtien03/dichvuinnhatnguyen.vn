<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class ErpIdempotency
{
    public static function run(Request $request, string $operation, array $payload, callable $execute)
    {
        $key = $request->header('Idempotency-Key');
        if ($key === null) {
            return DB::transaction($execute);
        }
        if (!preg_match('/\\A[A-Za-z0-9_-]{1,128}\\z/', $key)) {
            throw ValidationException::withMessages(['Idempotency-Key' => 'Invalid transaction key.']);
        }
        ksort($payload);
        $scope = hash('sha256', json_encode([$request->user()->id, $operation, $key], JSON_THROW_ON_ERROR));
        $fingerprint = hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR));
        return DB::transaction(function () use ($scope, $fingerprint, $execute) {
            DB::table('erp_request_keys')->insertOrIgnore(['scope'=>$scope,'fingerprint'=>$fingerprint]);
            $row = DB::table('erp_request_keys')->where('scope',$scope)->lockForUpdate()->first();
            abort_unless($row && hash_equals($row->fingerprint,$fingerprint),409,'Transaction key conflict.');
            if ($row->status !== null) {
                return response($row->response, (int) $row->status)->header('Content-Type','application/json');
            }
            $response = $execute();
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                throw new \Illuminate\Http\Exceptions\HttpResponseException($response);
            }
            DB::table('erp_request_keys')->where('scope',$scope)->update([
                'status'=>$response->getStatusCode(), 'response'=>$response->getContent(),
            ]);
            return $response;
        }, 3);
    }
}
