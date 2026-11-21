<?php

namespace App\Support;

use App\Models\Permission;

class AdminPermissionRegistry
{
    /**
     * Return the canonical list of sidebar permission definitions.
     */
    public static function definitions(): array
    {
        return [
            ['name' => 'Dashboard Access', 'slug' => 'admin.dashboard.view'],
            ['name' => 'Organization Section', 'slug' => 'admin.organizations.section'],
            ['name' => 'View Organizations', 'slug' => 'admin.organizations.view'],
            ['name' => 'Create Organization', 'slug' => 'admin.organizations.create'],
            ['name' => 'Manage Organization Types', 'slug' => 'admin.organization-types.view'],
            ['name' => 'Transactions Section', 'slug' => 'admin.transactions.section'],
            ['name' => 'View Transactions', 'slug' => 'admin.transactions.view'],
            ['name' => 'Consents Section', 'slug' => 'admin.consents.section'],
            ['name' => 'View Consents', 'slug' => 'admin.consents.view'],
            ['name' => 'Category Section', 'slug' => 'admin.categories.section'],
            ['name' => 'View Categories', 'slug' => 'admin.categories.view'],
            ['name' => 'Create Category', 'slug' => 'admin.categories.create'],
            ['name' => 'System Management Section', 'slug' => 'admin.system.section'],
            ['name' => 'Manage Roles', 'slug' => 'admin.roles.view'],
            ['name' => 'Manage Permissions', 'slug' => 'admin.permissions.view'],
            ['name' => 'View Admin Profile', 'slug' => 'admin.profile.view'],
            ['name' => 'Manage Team Members', 'slug' => 'admin.team-members.view'],
            ['name' => 'Delete Consents', 'slug' => 'admin.consents.delete'],
            ['name' => 'Sales Section', 'slug' => 'admin.sales.section'],
            ['name' => 'View Sales Dashboard', 'slug' => 'admin.sales.view'],
            ['name' => 'Plans Section', 'slug' => 'admin.plans.section'],
            ['name' => 'View Plans', 'slug' => 'admin.plans.view'],
        ];
    }

    /**
     * Ensure every sidebar permission exists in the database.
     */
    public static function sync(): void
    {
        foreach (self::definitions() as $definition) {
            Permission::firstOrCreate(
                ['slug' => $definition['slug']],
                ['name' => $definition['name']]
            );
        }
    }
}
