<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Make sure to import your User model
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define Permissions
        $permissions = [
            'view-dashboard',
            'manage-users', 'view-users', 'create-users', 'edit-users', 'delete-users',
            'manage-roles', 'view-roles', 'create-roles', 'edit-roles', 'delete-roles',
            'manage-permissions', 'view-permissions', // typically only for Super-Admin
            'manage-settings',
            'manage-trainings', 'view-trainings', 'create-trainings', 'edit-trainings', 'delete-trainings',
            'manage-events', 'view-events', 'create-events', 'edit-events', 'delete-events',
            'manage-resource-persons', 'view-resource-persons',
            'manage-memberships', 'view-memberships',
            'manage-payments', 'verify-payments',
            'manage-allowances', 'process-allowances',
            'manage-committees',
            'manage-elections',
            'manage-job-postings', 'view-job-applications',
            'manage-complaints',
            'access-admin-profile', // For admin's own profile
            // Add more permissions as your application grows
            'manage-memberships', 'view-memberships',
                        'manage-payment-accounts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Define Roles and Assign Permissions

        // Super-Admin (has all permissions)
        $superAdminRole = Role::firstOrCreate(['name' => 'Super-Admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo([
            'view-dashboard',
            'manage-users', 'view-users', 'create-users', 'edit-users', 'delete-users', // Or more granular
            'manage-trainings', 'view-trainings', 'create-trainings', 'edit-trainings',
            'manage-events', 'view-events', 'create-events', 'edit-events',
            'manage-resource-persons', 'view-resource-persons',
            'manage-memberships', 'view-memberships',
            'manage-payments', 'verify-payments',
            'manage-job-postings', 'view-job-applications',
            'manage-complaints',
            'access-admin-profile',

        ]);

        // Member
        $memberRole = Role::firstOrCreate(['name' => 'Member', 'guard_name' => 'web']);
        $memberRole->givePermissionTo([
            'view-dashboard', // Can view their own dashboard/profile area
            'view-trainings', // Can view available trainings
            'view-events',    // Can view available events
            // 'apply-for-allowance',
            // 'submit-complaint',
            'access-admin-profile', // For their own profile within the system
        ]);

        // Finance Admin
        $financeAdminRole = Role::firstOrCreate(['name' => 'Finance Admin', 'guard_name' => 'web']);
        $financeAdminRole->givePermissionTo([
            'view-dashboard',
            'manage-payments',
            'verify-payments',
            'view-memberships', // To see fee statuses
            'access-admin-profile',
        ]);

        // Finance Officer
        $financeOfficerRole = Role::firstOrCreate(['name' => 'Finance Officer', 'guard_name' => 'web']);
        $financeOfficerRole->givePermissionTo([
            'view-dashboard',
            'manage-payments', // Maybe only view and mark pending, verification by Finance Admin
            'verify-payments', // Or just 'process-payments'
            'access-admin-profile',
        ]);

        // Project/Allowance Manager
        $projectManagerRole = Role::firstOrCreate(['name' => 'Project/Allowance Manager', 'guard_name' => 'web']);
        $projectManagerRole->givePermissionTo([
            'view-dashboard',
            'manage-allowances',
            'process-allowances',
            'access-admin-profile',
        ]);

        // Resource Person
        $resourcePersonRole = Role::firstOrCreate(['name' => 'Resource Person', 'guard_name' => 'web']);
        $resourcePersonRole->givePermissionTo([
            'view-dashboard', // Their specific dashboard
            // 'manage-own-presentations',
            // 'view-assigned-trainings',
            'access-admin-profile', // For their own profile within the system
        ]);

        // General Applicant/Non-Member (might not need a role, or a very basic one)
        $generalApplicantRole = Role::firstOrCreate(['name' => 'General Applicant', 'guard_name' => 'web']);
        $generalApplicantRole->givePermissionTo([
            // 'view-public-trainings',
            // 'view-public-events',
            // 'access-own-application-profile',
        ]);


        // Create a Super Admin User
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'), // Change this in production!
                'email_verified_at' => now(),
                'user_type' => 'super_admin', // if you use this field
            ]
        );
        $superAdminUser->assignRole($superAdminRole);

        // Create an Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'), // Change this!
                'email_verified_at' => now(),
                'user_type' => 'admin',
            ]
        );
        $adminUser->assignRole($adminRole);

        // Create a Member User (Example)
        $memberUser = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'name' => 'Regular Member',
                'password' => Hash::make('password'), // Change this!
                'email_verified_at' => now(),
                'is_member' => true,
                'membership_start_date' => now(),
                'user_type' => 'member',
            ]
        );
        $memberUser->assignRole($memberRole);
    }
}
