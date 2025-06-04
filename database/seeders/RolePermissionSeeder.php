<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // For transaction

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Reset cached roles and permissions
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            // Define ALL POSSIBLE Permissions
            $permissions = [
                // General Admin Access
                'access-admin-panel', // Basic permission to even log into admin area
                'view-dashboard',     // For role-specific dashboards

                // User Management (Module 5.1)
                'manage-users',         // Umbrella permission
                'list-users', 'view-users',
                'create-users', 'store-users',
                'edit-users', 'update-users',
                'delete-users', 'destroy-users',
                'activate-users', 'deactivate-users',
                'assign-roles-to-users', 'remove-roles-from-users',
                'view-user-activity-logs',
                'impersonate-users', // Super-Admin/Admin for debugging
                'export-users-data',
                'manage-user-profiles', // If profiles are editable separately by admins
                'force-password-reset',

                // Membership Management (Module 5.2)
                'manage-memberships',   // Umbrella
                'list-memberships', 'view-memberships',
                'approve-membership-applications',
                'reject-membership-applications',
                'edit-membership-details', 'update-membership-details',
                'cancel-memberships', 'suspend-memberships',
                'manage-membership-fees', // Viewing due fees, recording payments
                'verify-membership-payments', // Specific to payment verification step
                'export-members-data',
                'view-member-payment-history',

                // Resource Person Management (Module 5.3)
                'manage-resource-persons', // Umbrella
                'list-resource-persons', 'view-resource-persons',
                'approve-resource-person-applications',
                'reject-resource-person-applications',
                'edit-resource-person-profiles', 'update-resource-person-profiles',
                'delete-resource-persons',
                'categorize-resource-persons', // Assigning to Journalist/Skill/IT
                'view-resource-person-activity', // Trainings done, hours, honorarium
                'manage-resource-person-content', // Uploads by RPs
                'approve-resource-person-content', 'reject-resource-person-content',
                'edit-resource-person-content', 'delete-resource-person-content',
                'invite-resource-persons-to-events', // Specific invitation action
                'track-resource-person-honorarium',
                'message-resource-persons',

                // Training Management (Module 5.4)
                'manage-trainings', // Umbrella
                'list-trainings', 'view-trainings',
                'create-trainings', 'store-trainings',
                'edit-trainings', 'update-trainings',
                'delete-trainings', 'destroy-trainings',
                'publish-trainings', 'unpublish-trainings',
                'manage-training-batches',
                'manage-training-schedules',
                'assign-training-instructors',
                'manage-training-registrations',
                'approve-training-registrations', 'reject-training-registrations',
                'view-training-participants',
                'generate-training-id-cards', // Action to trigger generation
                'generate-training-certificates', // Action to trigger generation
                'manage-training-signatories', // Define who signs certs/IDs
                'clone-trainings',
                'manage-training-target-audience', // Setting members/non-members/geo
                'manage-training-payment-settings',
                'message-training-participants',

                // Event Management (Module 5.5)
                'manage-events', // Umbrella
                'list-events', 'view-events',
                'create-events', 'store-events',
                'edit-events', 'update-events',
                'delete-events', 'destroy-events',
                'publish-events', 'unpublish-events',
                'manage-event-registrations',
                'approve-event-registrations', 'reject-event-registrations',
                'verify-event-payments',
                'clone-events',
                'manage-event-payment-settings',

                // Job Portal (Module 5.6)
                'manage-job-postings', // Umbrella
                'list-job-postings', 'view-job-postings',
                'create-job-postings', 'store-job-postings',
                'edit-job-postings', 'update-job-postings',
                'delete-job-postings', 'destroy-job-postings',
                'publish-job-postings',
                'manage-job-applications',
                'list-job-applications', 'view-job-applications',
                'shortlist-job-applicants',
                'reject-job-applicants',
                'download-applicant-cv',

                // Financial Management (Module 5.7)
                'manage-finances', // Umbrella
                'view-financial-dashboard',
                'manage-payment-accounts', // CRUD for Bkash/Nagad/Bank accounts of the org
                'record-income', 'record-expense',
                'transfer-funds-between-accounts',
                'verify-manual-payments', // Core manual verification step
                'approve-verified-payments', // Optional: secondary approval after verification
                'view-all-payments', // See payments from all sources
                'view-financial-reports',
                'export-financial-data',
                'manage-financial-categories',
                'reconcile-transactions',
                'view-payment-gateway-logs',

                // Committee Management (Module 5.8)
                'manage-committees', // Umbrella
                'list-committees', 'view-committees',
                'create-committees', 'store-committees',
                'edit-committees', 'update-committees',
                'delete-committees', 'destroy-committees',
                'assign-committee-managers',
                'manage-committee-members', // Add/remove members from a committee
                'send-committee-notifications',
                'manage-committee-types', // Central, division etc.
                'manage-committee-positions', // Creating position like President, Secretary for committees

                // Election System (Module 5.9)
                'manage-elections', // Umbrella
                'list-elections', 'view-elections',
                'create-elections', 'store-elections',
                'edit-elections', 'update-elections',
                'delete-elections', 'destroy-elections',
                'define-election-positions',
                'manage-election-nominations',
                'approve-election-nominations', 'reject-election-nominations',
                'withdraw-election-nominations', // Admin action
                'publish-election-candidate-lists',
                'declare-election-results', // (Manual declaration, online voting is future)
                'manage-election-schedule',
                'manage-election-nomination-fees',

                // Allowance Application Management (Module 5.10) - for Members
                'manage-allowance-applications', // Admin Umbrella
                'list-allowance-applications', 'view-allowance-applications', // Admin view
                'process-allowance-applications', // For Project/Allowance Manager
                'approve-allowance-applications', 'reject-allowance-applications',
                'request-allowance-documents',
                'verify-allowance-documents',
                'disburse-allowances', // Link with finance for payment
                'manage-allowance-types', // CRUD for allowance types (শিক্ষাবৃত্তি, চিকিৎসা)
                'view-member-allowance-history',

                // Notifications & Communication (Module 5.11)
                'manage-system-notifications', // CRUD for global notifications
                'send-global-notifications',
                'send-targeted-notifications', // To roles, geo-locations etc.
                'send-email-notifications',
                'manage-notification-templates', // If any

                // Complaints & Suggestions (Module 5.12)
                'manage-complaints-suggestions', // Umbrella
                'list-complaints-suggestions', 'view-complaints-suggestions',
                'reply-to-complaints-suggestions',
                'resolve-complaints-suggestions', 'close-complaints-suggestions',
                'view-anonymous-complaint-submitter', // Super-Admin only
                'download-complaint-data',

                // File Management (Module 5.13)
                'manage-all-files', // Super-Admin/Admin access to system-wide files
                'view-uploaded-files', // General viewing for relevant roles
                'delete-any-file', // Potentially dangerous

                // Frontend Website Integration (Module 5.14)
                'manage-frontend-content', // General CMS-like permission
                'manage-website-menus',
                'manage-website-sliders',
                'manage-website-pages',
                'manage-website-news',
                'manage-website-announcements-public',

                // Settings (Module 6 & General)
                'manage-general-settings', // Site name, logo, contact info
                'manage-payment-method-settings', // Configure payment methods shown to users
                'manage-email-settings', // SMTP, etc.
                'manage-api-settings', // For future mobile app keys etc.
                'view-system-logs', // Beyond user activity, server logs etc.
                'manage-maintenance-mode',
                'manage-database-backups', // Triggering or viewing status

                // Role & Permission Management (Spatie) - Typically Super-Admin
                'manage-roles', 'list-roles', 'view-roles', 'create-roles', 'store-roles', 'edit-roles', 'update-roles', 'delete-roles', 'destroy-roles',
                'manage-permissions', 'list-permissions', 'view-permissions', 'assign-permissions-to-roles',

                // Location Data Management (Divisions, Districts, Upazilas)
                'manage-divisions', 'list-divisions', 'create-divisions', 'edit-divisions', 'delete-divisions',
                'manage-districts', 'list-districts', 'create-districts', 'edit-districts', 'delete-districts',
                'manage-upazilas', 'list-upazilas', 'create-upazilas', 'edit-upazilas', 'delete-upazilas',
'manage-location-data', // General permission to manage all location data
                // Category Management (General)
                'manage-categories', 'list-categories', 'create-categories', 'edit-categories', 'delete-categories',

                // Specific permissions for non-admin roles (member, resource_person, applicant)
                // These define what they can *do* in the system, not what sections they see in admin panel.
                // For admin panel access, they'd need 'access-admin-panel' and specific 'view-*' for their dashboard.
                'apply-for-membership',
                'view-own-membership-status',
                'make-membership-payment', // Process of submitting payment proof
                'view-own-payment-history',

                'apply-for-resource-person',
                'manage-own-rp-profile',
                'upload-rp-content', // For Resource Person to upload their material
                'edit-own-rp-content', 'delete-own-rp-content', // If allowed before admin approval
                'view-assigned-rp-schedule',
                'accept-event-invitation', 'decline-event-invitation',

                'register-for-trainings',
                'register-for-events',
                'make-training-fee-payment',
                'make-event-fee-payment',
                'view-own-training-enrollments',
                'view-own-event-registrations',
                'download-own-id-card',
                'download-own-certificate',
                'view-own-activity-history', // Generic for users to see their interactions

                'apply-for-job',
                'upload-cv-for-job',

                'apply-for-allowance', // Member applying
                'view-own-allowance-application-status',
                'upload-allowance-documents', // Member uploading supporting docs

                'submit-complaint-suggestion',
                'view-own-complaints-suggestions',
                'reply-to-own-complaint-response',

                'access-own-profile', // To view/edit their own limited profile data
                'edit-own-profile',

                // API Access (General)
                'access-api',
            ];

            foreach ($permissions as $permissionName) {
                Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            }
            $this->command->info(count($permissions) . ' permissions created/verified.');

            // Define Roles
            $roles = [
                'Super-Admin',
                'Admin',
                'Finance Admin',
                'Finance Officer',
                'Project/Allowance Manager',
                'Committee Manager',
                'Registered Member',
                'Resource Person',
                'General Applicant/Non-Member' // Limited interaction, mainly frontend
            ];

            foreach ($roles as $roleName) {
                Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            }
            $this->command->info(count($roles) . ' roles created/verified.');

            // Assign Permissions to Roles

            // Super-Admin (gets all permissions)
            $superAdminRole = Role::findByName('Super-Admin');
            $superAdminRole->givePermissionTo(Permission::all());
            $this->command->info('Super-Admin assigned all permissions.');

            // Admin
            $adminRole = Role::findByName('Admin');
            $adminRole->givePermissionTo([
                'access-admin-panel', 'view-dashboard',
                'manage-users', 'list-users', 'view-users', 'create-users', 'edit-users', 'delete-users', 'activate-users', 'deactivate-users', 'assign-roles-to-users', 'view-user-activity-logs', 'export-users-data', 'manage-user-profiles', 'force-password-reset',
                'manage-memberships', 'list-memberships', 'view-memberships', 'approve-membership-applications', 'reject-membership-applications', 'edit-membership-details', 'cancel-memberships', 'manage-membership-fees', 'export-members-data', 'view-member-payment-history',
                'manage-resource-persons', 'list-resource-persons', 'view-resource-persons', 'approve-resource-person-applications', 'reject-resource-person-applications', 'edit-resource-person-profiles', 'delete-resource-persons', 'categorize-resource-persons', 'view-resource-person-activity', 'manage-resource-person-content', 'approve-resource-person-content', 'invite-resource-persons-to-events', 'message-resource-persons',
                'manage-trainings', 'list-trainings', 'view-trainings', 'create-trainings', 'edit-trainings', 'delete-trainings', 'publish-trainings', 'manage-training-batches', 'manage-training-schedules', 'assign-training-instructors', 'manage-training-registrations', 'approve-training-registrations', 'view-training-participants', 'generate-training-id-cards', 'generate-training-certificates', 'manage-training-signatories', 'clone-trainings', 'manage-training-target-audience', 'manage-training-payment-settings', 'message-training-participants',
                'manage-events', 'list-events', 'view-events', 'create-events', 'edit-events', 'delete-events', 'publish-events', 'manage-event-registrations', 'approve-event-registrations', 'verify-event-payments', 'clone-events', 'manage-event-payment-settings',
                'manage-job-postings', 'list-job-postings', 'view-job-postings', 'create-job-postings', 'edit-job-postings', 'delete-job-postings', 'publish-job-postings', 'manage-job-applications', 'list-job-applications', 'view-job-applications', 'shortlist-job-applicants', 'reject-job-applicants', 'download-applicant-cv',
                // 'manage-finances', // Finance Admin role is more specific
                'view-all-payments', 'view-financial-reports', // Can view, not manage all finance ops
                'manage-committees', 'list-committees', 'view-committees', 'create-committees', 'edit-committees', 'delete-committees', 'assign-committee-managers', 'manage-committee-members', 'send-committee-notifications', 'manage-committee-types', 'manage-committee-positions',
                'manage-elections', 'list-elections', 'view-elections', 'create-elections', 'edit-elections', 'delete-elections', 'define-election-positions', 'manage-election-nominations', 'approve-election-nominations', 'publish-election-candidate-lists', 'declare-election-results', 'manage-election-schedule', 'manage-election-nomination-fees',
                'manage-allowance-applications', 'list-allowance-applications', 'view-allowance-applications', 'manage-allowance-types', 'view-member-allowance-history', // Can view, Project Manager processes
                'manage-system-notifications', 'send-global-notifications', 'send-targeted-notifications',
                'manage-complaints-suggestions', 'list-complaints-suggestions', 'view-complaints-suggestions', 'reply-to-complaints-suggestions', 'resolve-complaints-suggestions', 'download-complaint-data',
                'manage-all-files', 'view-uploaded-files',
                'manage-frontend-content', 'manage-website-menus', 'manage-website-sliders', 'manage-website-pages', 'manage-website-news', 'manage-website-announcements-public',
                'manage-general-settings', 'manage-email-settings', // Limited settings
                'manage-location-data', 'list-divisions', 'create-divisions', 'edit-divisions', 'delete-divisions', 'list-districts', 'create-districts', 'edit-districts', 'delete-districts', 'list-upazilas', 'create-upazilas', 'edit-upazilas', 'delete-upazilas',
                'manage-categories', 'list-categories', 'create-categories', 'edit-categories', 'delete-categories',
                'access-own-profile', 'edit-own-profile',
            ]);
            $this->command->info('Admin role assigned permissions.');

            // Finance Admin
            $financeAdminRole = Role::findByName('Finance Admin');
            $financeAdminRole->givePermissionTo([
                'access-admin-panel', 'view-dashboard',
                'manage-finances',
                'manage-payment-accounts',
                'record-income', 'record-expense',
                'transfer-funds-between-accounts',
                'verify-manual-payments',
                'approve-verified-payments',
                'view-all-payments',
                'view-financial-reports', 'export-financial-data',
                'manage-financial-categories',
                'reconcile-transactions',
                'view-payment-gateway-logs',
                'view-memberships', // To check fee status for verification
                'view-member-payment-history',
                'manage-membership-fees', // To see amounts due
                'verify-membership-payments',
                'verify-event-payments',
                'disburse-allowances', // Final payment step for allowances
                'track-resource-person-honorarium', // Payout honorariums
                'manage-payment-method-settings',
                'access-own-profile', 'edit-own-profile',
            ]);
            $this->command->info('Finance Admin role assigned permissions.');

            // Finance Officer
            $financeOfficerRole = Role::findByName('Finance Officer');
            $financeOfficerRole->givePermissionTo([
                'access-admin-panel', 'view-dashboard',
                'record-income', 'record-expense', // Data entry
                'verify-manual-payments', // Initial verification
                'view-all-payments', // Can see payments for verification
                'view-financial-reports', // View access
                'view-member-payment-history',
                'access-own-profile', 'edit-own-profile',
            ]);
            $this->command->info('Finance Officer role assigned permissions.');

            // Project/Allowance Manager
            $projectManagerRole = Role::findByName('Project/Allowance Manager');
            $projectManagerRole->givePermissionTo([
                'access-admin-panel', 'view-dashboard',
                'manage-allowance-applications',
                'list-allowance-applications', 'view-allowance-applications',
                'process-allowance-applications',
                'approve-allowance-applications', 'reject-allowance-applications',
                'request-allowance-documents',
                'verify-allowance-documents',
                // 'disburse-allowances', // This might be Finance Admin's job
                'manage-allowance-types',
                'view-member-allowance-history',
                'access-own-profile', 'edit-own-profile',
            ]);
            $this->command->info('Project/Allowance Manager role assigned permissions.');

            // Committee Manager
            $committeeManagerRole = Role::findByName('Committee Manager');
            $committeeManagerRole->givePermissionTo([
                'access-admin-panel', 'view-dashboard', // Dashboard specific to their committee
                // Permissions below would be scoped by application logic to their assigned committee
                'view-committees', // View details of their own committee
                'manage-committee-members', // Add/remove members in their committee
                'send-committee-notifications', // To members of their committee
                'view-memberships', // To remind members about fees (scoped to their committee)
                'access-own-profile', 'edit-own-profile',
            ]);
            $this->command->info('Committee Manager role assigned permissions.');

            // Registered Member
            $memberRole = Role::findByName('Registered Member');
            $memberRole->givePermissionTo([
                'access-admin-panel', // To access their own dashboard/profile within the system
                'view-dashboard',     // Their member dashboard
                'view-trainings',
                'view-events',
                'apply-for-membership', // Initial application
                'view-own-membership-status',
                'make-membership-payment',
                'view-own-payment-history',
                'register-for-trainings', 'make-training-fee-payment',
                'register-for-events', 'make-event-fee-payment',
                'view-own-training-enrollments', 'view-own-event-registrations',
                'download-own-id-card', 'download-own-certificate',
                'apply-for-job', 'upload-cv-for-job',
                'apply-for-allowance', 'view-own-allowance-application-status', 'upload-allowance-documents',
                'submit-complaint-suggestion', 'view-own-complaints-suggestions', 'reply-to-own-complaint-response',
                'access-own-profile', 'edit-own-profile',
                'view-own-activity-history',
                'access-api', // If mobile app is for members
            ]);
            $this->command->info('Registered Member role assigned permissions.');

            // Resource Person
            $resourcePersonRole = Role::findByName('Resource Person');
            $resourcePersonRole->givePermissionTo([
                'access-admin-panel', // To access their own dashboard/profile within the system
                'view-dashboard',     // Their RP dashboard
                'apply-for-resource-person', // Initial application
                'manage-own-rp-profile',
                'upload-rp-content',
                'edit-own-rp-content', 'delete-own-rp-content',
                'view-assigned-rp-schedule',
                'accept-event-invitation', 'decline-event-invitation',
                'view-trainings', // View trainings they might be part of
                'access-own-profile', 'edit-own-profile',
                'view-own-activity-history',
                'access-api', // If mobile app has RP features
            ]);
            $this->command->info('Resource Person role assigned permissions.');

            // General Applicant/Non-Member
            $generalApplicantRole = Role::findByName('General Applicant/Non-Member');
            $generalApplicantRole->givePermissionTo([
                // Mostly frontend interactions, if they need to log in to see application status:
                // 'access-admin-panel', // Very limited view, just application status page
                // 'view-dashboard', // A very simple dashboard for their applications
                'register-for-trainings', // If non-members can register
                'register-for-events',    // If non-members can register
                'make-training-fee-payment',
                'make-event-fee-payment',
                'view-own-training-enrollments',
                'view-own-event-registrations',
                'submit-complaint-suggestion', // If anonymous or non-member submission is allowed
                'access-api', // If non-members can use parts of the API (e.g. public event list)
            ]);
            $this->command->info('General Applicant role assigned permissions.');


            // --- Create Seed Users ---

            // Super Admin User
            $superAdminUser = User::firstOrCreate(
                ['email' => 'superadmin@example.com'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password'), // CHANGE THIS!
                    'email_verified_at' => now(),
                ]
            );
            if ($superAdminUser->wasRecentlyCreated || !$superAdminUser->hasRole('Super-Admin')) {
                $superAdminUser->assignRole('Super-Admin');
                $this->command->info('Super Admin user created/role assigned.');
            }


            // Admin User
            $adminUser = User::firstOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'Administrator',
                    'password' => Hash::make('password'), // CHANGE THIS!
                    'email_verified_at' => now(),
                ]
            );
            if ($adminUser->wasRecentlyCreated || !$adminUser->hasRole('Admin')) {
                $adminUser->assignRole('Admin');
                $this->command->info('Admin user created/role assigned.');
            }

            // Finance Admin User
            $financeAdminUser = User::firstOrCreate(
                ['email' => 'financeadmin@example.com'],
                [
                    'name' => 'Finance Administrator',
                    'password' => Hash::make('password'), // CHANGE THIS!
                    'email_verified_at' => now(),
                ]
            );
            if ($financeAdminUser->wasRecentlyCreated || !$financeAdminUser->hasRole('Finance Admin')) {
                $financeAdminUser->assignRole('Finance Admin');
                $this->command->info('Finance Admin user created/role assigned.');
            }

            // Example Member User
            $memberUser = User::firstOrCreate(
                ['email' => 'member@example.com'],
                [
                    'name' => 'Registered Member One',
                    'password' => Hash::make('password'), // CHANGE THIS!
                    'email_verified_at' => now(),
                ]
            );
            if ($memberUser->wasRecentlyCreated || !$memberUser->hasRole('Registered Member')) {
                $memberUser->assignRole('Registered Member');
                $this->command->info('Example Member user created/role assigned.');
            }

            // Example Resource Person User
            $resourcePersonUser = User::firstOrCreate(
                ['email' => 'resourceperson@example.com'],
                [
                    'name' => 'Expert Resource Person',
                    'password' => Hash::make('password'), // CHANGE THIS!
                    'email_verified_at' => now(),
                ]
            );
            if ($resourcePersonUser->wasRecentlyCreated || !$resourcePersonUser->hasRole('Resource Person')) {
                $resourcePersonUser->assignRole('Resource Person');
                $this->command->info('Example Resource Person user created/role assigned.');
            }


            $this->command->info('Role and Permission Seeding Completed Successfully.');
        });
    }
}
