<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public const PERMISSIONS = [
        'dashboard.view',
        'products.view', 'products.manage',
        'categories.manage', 'brands.manage', 'attributes.manage', 'collections.manage',
        'inventory.manage',
        'orders.view', 'orders.manage',
        'customers.view', 'customers.manage',
        'coupons.manage', 'flash-sales.manage',
        'reviews.manage',
        'banners.manage', 'testimonials.manage',
        'blog.manage', 'pages.manage', 'faqs.manage',
        'shipping.manage',
        'newsletter.manage', 'messages.manage',
        'reports.view',
        'settings.manage',
        'staff.manage',
        'logs.view',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::PERMISSIONS as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        $super = Role::findOrCreate('super_admin', 'web');
        $admin = Role::findOrCreate('admin', 'web');
        $manager = Role::findOrCreate('manager', 'web');
        $editor = Role::findOrCreate('editor', 'web');
        Role::findOrCreate('customer', 'web');

        $super->syncPermissions(Permission::all());
        $admin->syncPermissions(collect(self::PERMISSIONS)->reject(fn ($p) => in_array($p, ['staff.manage', 'settings.manage']))->all());
        $manager->syncPermissions([
            'dashboard.view', 'products.view', 'products.manage', 'inventory.manage',
            'orders.view', 'orders.manage', 'customers.view', 'coupons.manage',
            'flash-sales.manage', 'reviews.manage', 'reports.view', 'messages.manage',
        ]);
        $editor->syncPermissions([
            'dashboard.view', 'products.view', 'banners.manage', 'testimonials.manage',
            'blog.manage', 'pages.manage', 'faqs.manage',
        ]);

        $owner = User::firstOrCreate(
            ['email' => 'admin@nokshi.test'],
            ['name' => 'Nokshi Owner', 'phone' => '01700000000', 'password' => 'password', 'email_verified_at' => now()]
        );
        $owner->syncRoles(['super_admin']);

        $staff = User::firstOrCreate(
            ['email' => 'manager@nokshi.test'],
            ['name' => 'Rafi Ahmed', 'phone' => '01800000000', 'password' => 'password', 'email_verified_at' => now()]
        );
        $staff->syncRoles(['manager']);

        $customer = User::firstOrCreate(
            ['email' => 'customer@nokshi.test'],
            ['name' => 'Tasnim Rahman', 'phone' => '01900000000', 'password' => 'password', 'email_verified_at' => now()]
        );
        $customer->syncRoles(['customer']);
        $customer->addresses()->firstOrCreate(['label' => 'Home'], [
            'name' => 'Tasnim Rahman', 'phone' => '01900000000', 'email' => 'customer@nokshi.test',
            'address_line' => 'House 12, Road 5, Block C', 'area' => 'Banani', 'city' => 'Dhaka',
            'district' => 'Dhaka', 'postal_code' => '1213', 'is_default' => true,
        ]);
    }
}
