<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->mapWithKeys(function ($item) {
            $decoded = json_decode($item->value, true);
            $val = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $item->value;
            return [$item->key => $val];
        });

        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            if ($key === '_token') continue;
            SiteSetting::set($key, $value);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật cấu hình website thành công',
            'data' => $this->index()->original['data'],
        ]);
    }
}

