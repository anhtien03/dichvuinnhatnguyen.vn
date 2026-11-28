<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationSecurityTest extends TestCase
{
    private User $staff;
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Schema::create('roles', function(Blueprint $t) { $t->id(); $t->string('slug'); });
        Schema::create('users', function(Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email')->unique(); $t->string('password');
            $t->unsignedBigInteger('role_id')->nullable(); $t->boolean('is_active')->default(true);
            $t->text('custom_permissions')->nullable(); $t->timestamps();
        });
        Schema::create('personal_access_tokens', function(Blueprint $t) {
            $t->id(); $t->morphs('tokenable'); $t->text('name'); $t->string('token',64)->unique();
            $t->text('abilities')->nullable(); $t->timestamp('last_used_at')->nullable();
            $t->timestamp('expires_at')->nullable(); $t->timestamps();
        });
        $this->staff=User::create(['name'=>'Fixture','email'=>'staff@example.test','password'=>'fixture-password','is_active'=>true]);
    }
    private function login(string $password='fixture-password')
    {
        return $this->postJson('/api/v1/auth/login',['email'=>$this->staff->email,'password'=>$password]);
    }
    private function tokenRequest(string $token, string $method='GET', string $path='/api/v1/auth/me')
    {
        Auth::forgetGuards();
        Auth::shouldUse('web');
        return $this->json($method,$path,[],['Authorization'=>'Bearer '.$token]);
    }
    public function test_wrong_password_and_locked_login_are_denied(): void
    {
        $this->login('wrong')->assertUnprocessable();
        $this->staff->update(['is_active'=>false]);
        $this->login()->assertUnprocessable();
        $this->assertSame(0,$this->staff->tokens()->count());
    }
    public function test_login_issues_expiring_hashed_token(): void
    {
        $token=$this->login()->assertOk()->json('token');
        $record=$this->staff->tokens()->firstOrFail();
        $this->assertSame(hash('sha256',explode('|',$token,2)[1]),$record->token);
        $this->assertNotNull($record->expires_at);
        $this->tokenRequest($token)->assertOk()->assertJsonPath('user.id',$this->staff->id);
    }
    public function test_logout_revokes_only_current_device(): void
    {
        $a=$this->login()->assertOk()->json('token');
        $b=$this->login()->assertOk()->json('token');
        $this->tokenRequest($a,'POST','/api/v1/auth/logout')->assertOk();
        $this->tokenRequest($a)->assertUnauthorized();
        $this->tokenRequest($b)->assertOk();
        $this->assertSame(1,$this->staff->tokens()->count());
    }
    public function test_expired_and_revoked_tokens_are_rejected(): void
    {
        $expired=$this->staff->createToken('expired',['*'],now()->subMinute())->plainTextToken;
        $this->tokenRequest($expired)->assertUnauthorized();
        $token=$this->staff->createToken('valid')->plainTextToken;
        $this->staff->tokens()->delete();
        $this->tokenRequest($token)->assertUnauthorized();
    }
    public function test_locked_account_with_existing_token_is_denied(): void
    {
        $token=$this->staff->createToken('fixture')->plainTextToken;
        $this->staff->update(['is_active'=>false]);
        $this->tokenRequest($token)->assertForbidden();
        $this->tokenRequest($token,'GET','/api/v1/admin/dashboard-stats')->assertForbidden();
    }
    public function test_login_prunes_expired_tokens_only(): void
    {
        $old=$this->staff->createToken('expired',['*'],now()->subMinute());
        $valid=$this->staff->createToken('valid',['*'],now()->addHour());
        $this->login()->assertOk();
        $this->assertDatabaseMissing('personal_access_tokens',['id'=>$old->accessToken->id]);
        $this->assertDatabaseHas('personal_access_tokens',['id'=>$valid->accessToken->id]);
        $this->assertSame(2,$this->staff->tokens()->count());
    }
    public function test_login_rate_limit(): void
    {
        for ($i=0;$i<10;$i++) $this->login('wrong')->assertUnprocessable();
        $this->login('wrong')->assertStatus(429)->assertHeader('Retry-After');
    }
}
