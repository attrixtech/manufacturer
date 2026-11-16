<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define Modules & Operations
        $modules = [
            'companies' => ['view', 'create', 'edit', 'delete'],
            'branches' => ['view', 'create', 'edit', 'delete'],
            'users' => ['view', 'create', 'edit', 'delete'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'products' => ['view', 'create', 'edit', 'delete', 'export'],
            'inventory' => ['view', 'create', 'edit', 'adjust', 'transfer', 'export'],
            'procurement' => ['view', 'create', 'edit', 'delete', 'approve', 'grn', 'export'],
            'bom' => ['view', 'create', 'edit', 'delete', 'approve'],
            'work_orders' => ['view', 'create', 'edit', 'release', 'complete', 'export'],
            'mrp' => ['view', 'run', 'export'],
            'quality' => ['view', 'inspect', 'approve', 'reject'],
            'maintenance' => ['view', 'schedule', 'log_breakdown', 'complete'],
            'sales' => ['view', 'create', 'edit', 'delete', 'approve', 'dispatch', 'export'],
            'accounting' => ['view', 'create_journal', 'view_reports', 'close_period', 'export'],
            'reports' => ['view', 'export'],
            'settings' => ['view', 'edit'],
        ];

        $allPermissions = [];
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                $permName = "{$module}.{$action}";
                $permission = Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                $allPermissions[] = $permission;
            }
        }

        // 2. Create Roles and assign permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Administrator', 'guard_name' => 'web']);
        $superAdmin->syncPermissions(Permission::all());

        $plantManager = Role::firstOrCreate(['name' => 'Plant Manager', 'guard_name' => 'web']);
        $plantManager->syncPermissions([
            'products.view', 'products.create', 'products.edit', 'products.export',
            'inventory.view', 'inventory.transfer', 'inventory.export',
            'procurement.view', 'procurement.approve', 'procurement.export',
            'bom.view', 'bom.approve',
            'work_orders.view', 'work_orders.create', 'work_orders.release', 'work_orders.complete', 'work_orders.export',
            'mrp.view', 'mrp.run', 'mrp.export',
            'quality.view', 'quality.approve',
            'maintenance.view', 'maintenance.schedule',
            'reports.view', 'reports.export',
        ]);

        $productionHead = Role::firstOrCreate(['name' => 'Production Head', 'guard_name' => 'web']);
        $productionHead->syncPermissions([
            'products.view',
            'bom.view', 'bom.create', 'bom.edit', 'bom.approve',
            'work_orders.view', 'work_orders.create', 'work_orders.edit', 'work_orders.release', 'work_orders.complete', 'work_orders.export',
            'mrp.view', 'mrp.run',
            'inventory.view', 'inventory.transfer',
            'reports.view',
        ]);

        $qualityInspector = Role::firstOrCreate(['name' => 'Quality Inspector', 'guard_name' => 'web']);
        $qualityInspector->syncPermissions([
            'products.view',
            'procurement.view', 'procurement.grn',
            'quality.view', 'quality.inspect', 'quality.approve', 'quality.reject',
            'work_orders.view',
            'reports.view',
        ]);

        $inventoryManager = Role::firstOrCreate(['name' => 'Warehouse & Inventory Manager', 'guard_name' => 'web']);
        $inventoryManager->syncPermissions([
            'products.view', 'products.create', 'products.edit', 'products.export',
            'inventory.view', 'inventory.create', 'inventory.edit', 'inventory.adjust', 'inventory.transfer', 'inventory.export',
            'procurement.view', 'procurement.grn',
            'sales.dispatch',
            'reports.view',
        ]);

        $purchaseManager = Role::firstOrCreate(['name' => 'Procurement Manager', 'guard_name' => 'web']);
        $purchaseManager->syncPermissions([
            'products.view',
            'procurement.view', 'procurement.create', 'procurement.edit', 'procurement.delete', 'procurement.export',
            'inventory.view',
            'reports.view',
        ]);

        $salesManager = Role::firstOrCreate(['name' => 'Sales & Dispatch Manager', 'guard_name' => 'web']);
        $salesManager->syncPermissions([
            'products.view',
            'sales.view', 'sales.create', 'sales.edit', 'sales.delete', 'sales.approve', 'sales.dispatch', 'sales.export',
            'inventory.view',
            'reports.view',
        ]);

        $accountsManager = Role::firstOrCreate(['name' => 'Accounts & Finance Manager', 'guard_name' => 'web']);
        $accountsManager->syncPermissions([
            'accounting.view', 'accounting.create_journal', 'accounting.view_reports', 'accounting.close_period', 'accounting.export',
            'procurement.view', 'procurement.export',
            'sales.view', 'sales.export',
            'inventory.view', 'inventory.export',
            'reports.view', 'reports.export',
        ]);

        $operator = Role::firstOrCreate(['name' => 'Machine Operator', 'guard_name' => 'web']);
        $operator->syncPermissions([
            'work_orders.view',
            'maintenance.view', 'maintenance.log_breakdown',
        ]);
    }
}
