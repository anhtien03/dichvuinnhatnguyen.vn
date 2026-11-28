<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class QuoteFileSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Storage::fake('public');
        Storage::fake('local');
        Mail::fake();
        Schema::create('quote_requests', function (Blueprint $t) {
            $t->id();
            foreach (['code','customer_name','phone','email','company','product_name','quantity','specs_description','file_path','file_name','file_type','status'] as $field) $t->text($field)->nullable();
            $t->unsignedBigInteger('product_id')->nullable();
            $t->unsignedBigInteger('file_size')->nullable();
            $t->timestamps();
        });
    }

    private function upload(string $name = 'design.pdf')
    {
        return $this->post('/api/v1/quotes', [
            'customer_name'=>'Fixture', 'phone'=>'0900000000',
            'file'=>UploadedFile::fake()->createWithContent($name, "%PDF-1.4\n%%EOF\n"),
        ], ['Accept'=>'application/json']);
    }

    public function test_executable_extension_is_rejected(): void
    {
        $this->upload('design.php')->assertUnprocessable();
        $this->assertDatabaseCount('quote_requests',0);
        $this->assertSame([],Storage::disk('public')->allFiles());
    }

    public function test_target_valid_upload_is_private(): void
    {
        $this->upload()->assertCreated();
        $this->assertDatabaseCount('quote_requests',1);
        $this->assertSame([],Storage::disk('public')->allFiles());
        $this->assertCount(1,Storage::disk('local')->allFiles('quotes'));
    }

    public function test_target_unapproved_extension_is_rejected(): void
    {
        $this->upload('design.bin')->assertUnprocessable();
        $this->assertDatabaseCount('quote_requests',0);
    }

    public function test_target_download_requires_authentication(): void
    {
        $this->getJson('/api/v1/admin/quotes/1/file')->assertUnauthorized();
    }

    public function test_target_download_requires_quote_permission(): void
    {
        $u = new User(['is_active'=>true,'custom_permissions'=>['quotes.view'=>false]]);
        $u->setRelation('role',null);
        Sanctum::actingAs($u);
        $this->getJson('/api/v1/admin/quotes/1/file')->assertForbidden();
    }
}
