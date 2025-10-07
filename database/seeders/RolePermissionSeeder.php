<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Patient permissions
            'view-own-profile',
            'update-own-profile',
            'view-own-appointments',
            'create-appointment',
            'cancel-own-appointment',
            'reschedule-own-appointment',
            
            // Dentist permissions
            'view-dentist-profile',
            'update-dentist-profile',
            'view-dentist-appointments',
            'view-dentist-patients',
            'update-appointment-status',
            'create-medical-history',
            'view-medical-history',
            'create-treatment',
            'update-treatment',
            
            // Admin permissions
            'view-all-users',
            'create-user',
            'update-user',
            'delete-user',
            'view-all-appointments',
            'view-all-patients',
            'view-all-dentists',
            'view-reports',
            'manage-specializations',
            'manage-procedures',
            'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $dentistRole = Role::create(['name' => 'dentist']);
        $dentistRole->givePermissionTo([
            'view-dentist-profile',
            'update-dentist-profile',
            'view-dentist-appointments',
            'view-dentist-patients',
            'update-appointment-status',
            'create-medical-history',
            'view-medical-history',
            'create-treatment',
            'update-treatment',
        ]);

        $patientRole = Role::create(['name' => 'patient']);
        $patientRole->givePermissionTo([
            'view-own-profile',
            'update-own-profile',
            'view-own-appointments',
            'create-appointment',
            'cancel-own-appointment',
            'reschedule-own-appointment',
        ]);
    }
}
