<?php

namespace Tests\Feature;

use App\Models\Applicant;
use App\Models\Institution;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_with_applicant_profile(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Aisha',
            'last_name' => 'Kamara',
            'email' => 'aisha@example.com',
        ]);

        $applicant = Applicant::create([
            'user_id' => $user->id,
            'nationality' => 'Sierra Leonean',
        ]);

        $this->assertTrue($applicant->user->is($user));
        $this->assertTrue($user->applicant->is($applicant));
    }

    public function test_institution_has_unique_membership_and_user_role_scope(): void
    {
        $institution = Institution::create([
            'name' => 'Limkokwing University',
            'slug' => 'limkokwing',
            'institution_code' => 'LK',
            'status' => 'active',
        ]);

        $user = User::factory()->create();
        $role = Role::create([
            'name' => 'Admissions Officer',
            'slug' => 'admissions-officer-test',
            'scope' => 'institution',
        ]);

        $institution->users()->attach($user->id, [
            'status' => 'active',
            'joined_at' => now(),
        ]);

        $user->roles()->attach($role->id, [
            'institution_id' => $institution->id,
        ]);

        $this->assertTrue($institution->users()->where('users.id', $user->id)->exists());
        $this->assertTrue($user->roles()->wherePivot('institution_id', $institution->id)->exists());
    }

    public function test_role_permissions_can_be_assigned(): void
    {
        $role = Role::create([
            'name' => 'University Administrator',
            'slug' => 'university-administrator-test',
            'scope' => 'institution',
        ]);

        $permission = Permission::create([
            'name' => 'Manage programmes',
            'slug' => 'manage-programmes-test',
            'group' => 'institution',
        ]);

        $role->permissions()->attach($permission);

        $this->assertTrue($role->permissions()->where('permissions.id', $permission->id)->exists());
    }
}
