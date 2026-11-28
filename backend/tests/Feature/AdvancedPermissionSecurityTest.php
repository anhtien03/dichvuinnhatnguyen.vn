<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdvancedPermissionSecurityTest extends TestCase
{
    private User $staff;
    private User $root;
    protected function setUp(): void
    {
        parent::setUp();
        $this->assertSame('testing', app()->environment());
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        Schema::create('roles', function(Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('slug')->unique(); $t->timestamps();
        });
        Schema::create('users', function(Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('email')->unique(); $t->string('password');
            $t->unsignedBigInteger('role_id'); $t->string('phone')->nullable();
            $t->boolean('is_active')->default(true); $t->text('custom_permissions')->nullable(); $t->timestamps();
        });
        $rootRole = Role::create(['name'=>'Root fixture','slug'=>'super_admin']);
        $staffRole = Role::create(['name'=>'Staff fixture','slug'=>'staff']);
        $this->root = User::create(['name'=>'Root','email'=>'root@example.test','password'=>'fixture-password','role_id'=>$rootRole->id,'is_active'=>true]);
        $this->staff = User::create(['name'=>'Staff','email'=>'staff@example.test','password'=>'fixture-password','role_id'=>$staffRole->id,'is_active'=>true,'custom_permissions'=>['users.manage'=>true]]);
        Sanctum::actingAs($this->staff);
    }

    private function payload(User $u, array $changes): array
    {
        return array_merge(['name'=>$u->name,'email'=>$u->email,'role_id'=>$u->role_id,'is_active'=>true], $changes);
    }

    public function test_self_promotion_is_denied(): void
    {
        $response = $this->putJson('/api/v1/admin/users/'.$this->staff->id, $this->payload($this->staff,['role_id'=>$this->root->role_id]));
        $this->assertSame($this->staff->role_id, $this->staff->fresh()->role_id);
        $response->assertForbidden();
    }

    public function test_root_edit_is_denied(): void
    {
        $response = $this->putJson('/api/v1/admin/users/'.$this->root->id, $this->payload($this->root,['name'=>'Changed fixture']));
        $this->assertSame('Root', $this->root->fresh()->name);
        $response->assertForbidden();
    }

    public function test_grant_unheld_permission_is_denied(): void
    {
        $response = $this->putJson('/api/v1/admin/users/'.$this->staff->id, $this->payload($this->staff,['custom_permissions'=>['users.manage'=>true,'finance.dashboard'=>true]]));
        $this->assertFalse($this->staff->fresh()->hasPermission('finance.dashboard'));
        $response->assertForbidden();
    }

    public function test_last_root_cannot_be_demoted(): void
    {
        Sanctum::actingAs($this->root);
        $response = $this->putJson('/api/v1/admin/users/'.$this->root->id, $this->payload($this->root,['role_id'=>$this->staff->role_id]));
        $this->assertSame($this->root->role_id, $this->root->fresh()->role_id);
        $this->assertContains($response->status(), [403,422]);
    }

    public function test_string_false_is_not_a_permission_grant(): void
    {
        $u = new User(['is_active'=>true,'custom_permissions'=>['finance.dashboard'=>'false']]);
        $u->setRelation('role',null);
        $this->assertFalse($u->hasPermission('finance.dashboard'));
    }
}
