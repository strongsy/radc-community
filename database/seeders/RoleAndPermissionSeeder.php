<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // permission permissions
        Permission::create(['name' => 'permission-registrations']);
        Permission::create(['name' => 'permission-create']);
        Permission::create(['name' => 'permission-read']);
        Permission::create(['name' => 'permission-update']);
        Permission::create(['name' => 'permission-destroy']);

        Permission::create(['name' => 'activity-log-read']);

        // role permissions
        Permission::create(['name' => 'role-registrations']);
        Permission::create(['name' => 'role-create']);
        Permission::create(['name' => 'role-read']);
        Permission::create(['name' => 'role-update']);
        Permission::create(['name' => 'role-destroy']);

        // mail permissions
        Permission::create(['name' => 'mail-registrations']);
        Permission::create(['name' => 'mail-create']);
        Permission::create(['name' => 'mail-read']);
        Permission::create(['name' => 'mail-update']);
        Permission::create(['name' => 'mail-send']);
        Permission::create(['name' => 'mail-restore']);
        Permission::create(['name' => 'mail-force-delete']);
        Permission::create(['name' => 'mail-destroy']);
        Permission::create(['name' => 'mail-reply']);

        // reply permissions
        Permission::create(['name' => 'reply-registrations']);
        Permission::create(['name' => 'reply-create']);
        Permission::create(['name' => 'reply-read']);
        Permission::create(['name' => 'reply-update']);
        Permission::create(['name' => 'reply-send']);
        Permission::create(['name' => 'reply-restore']);
        Permission::create(['name' => 'reply-force-delete']);
        Permission::create(['name' => 'reply-destroy']);

        // registrant permissions
        Permission::create(['name' => 'registrant-registrations']);
        Permission::create(['name' => 'registrant-create']);
        Permission::create(['name' => 'registrant-read']);
        Permission::create(['name' => 'registrant-update']);
        Permission::create(['name' => 'registrant-destroy']);
        Permission::create(['name' => 'registrant-authorize']);

        // users permissions
        Permission::create(['name' => 'user-approve']);
        Permission::create(['name' => 'user-registrations']);
        Permission::create(['name' => 'user-create']);
        Permission::create(['name' => 'user-read']);
        Permission::create(['name' => 'user-update']);
        Permission::create(['name' => 'user-block']);
        Permission::create(['name' => 'user-action']);
        Permission::create(['name' => 'user-unblock']);
        Permission::create(['name' => 'user-destroy']);

        // event permissions
        Permission::create(['name' => 'event-registrations']);
        Permission::create(['name' => 'event-create']);
        Permission::create(['name' => 'event-read']);
        Permission::create(['name' => 'event-publish']);
        Permission::create(['name' => 'event-unpublish']);
        Permission::create(['name' => 'event-approve']);
        Permission::create(['name' => 'event-unapproved']);
        Permission::create(['name' => 'event-update']);
        Permission::create(['name' => 'event-destroy']);
        Permission::create(['name' => 'event-restore']);
        Permission::create(['name' => 'event-force-delete']);

        // post permissions
        Permission::create(['name' => 'post-registrations']);
        Permission::create(['name' => 'post-create']);
        Permission::create(['name' => 'post-read']);
        Permission::create(['name' => 'post-publish']);
        Permission::create(['name' => 'post-unpublish']);
        Permission::create(['name' => 'post-approve']);
        Permission::create(['name' => 'post-unapproved']);
        Permission::create(['name' => 'post-update']);
        Permission::create(['name' => 'post-destroy']);
        Permission::create(['name' => 'post-restore']);
        Permission::create(['name' => 'post-force-delete']);

        // article permissions
        Permission::create(['name' => 'article-registrations']);
        Permission::create(['name' => 'article-create']);
        Permission::create(['name' => 'article-read']);
        Permission::create(['name' => 'article-publish']);
        Permission::create(['name' => 'article-unpublish']);
        Permission::create(['name' => 'article-approve']);
        Permission::create(['name' => 'article-unapproved']);
        Permission::create(['name' => 'article-update']);
        Permission::create(['name' => 'article-destroy']);
        Permission::create(['name' => 'article-restore']);
        Permission::create(['name' => 'article-force-delete']);

        // story permissions
        Permission::create(['name' => 'story-registrations']);
        Permission::create(['name' => 'story-create']);
        Permission::create(['name' => 'story-read']);
        Permission::create(['name' => 'story-publish']);
        Permission::create(['name' => 'story-unpublish']);
        Permission::create(['name' => 'story-approve']);
        Permission::create(['name' => 'story-unapproved']);
        Permission::create(['name' => 'story-update']);
        Permission::create(['name' => 'story-destroy']);
        Permission::create(['name' => 'story-restore']);
        Permission::create(['name' => 'story-force-delete']);

        // gallery permissions
        Permission::create(['name' => 'gallery-registrations']);
        Permission::create(['name' => 'gallery-create']);
        Permission::create(['name' => 'gallery-read']);
        Permission::create(['name' => 'gallery-publish']);
        Permission::create(['name' => 'gallery-unpublish']);
        Permission::create(['name' => 'gallery-approve']);
        Permission::create(['name' => 'gallery-unapproved']);
        Permission::create(['name' => 'gallery-update']);
        Permission::create(['name' => 'gallery-destroy']);
        Permission::create(['name' => 'gallery-restore']);
        Permission::create(['name' => 'gallery-force-delete']);

        // album permissions
        Permission::create(['name' => 'album-registrations']);
        Permission::create(['name' => 'album-create']);
        Permission::create(['name' => 'album-read']);
        Permission::create(['name' => 'album-publish']);
        Permission::create(['name' => 'album-unpublish']);
        Permission::create(['name' => 'album-approve']);
        Permission::create(['name' => 'album-unapproved']);
        Permission::create(['name' => 'album-update']);
        Permission::create(['name' => 'album-destroy']);
        Permission::create(['name' => 'album-restore']);
        Permission::create(['name' => 'album-force-delete']);

        // image permissions
        Permission::create(['name' => 'image-registrations']);
        Permission::create(['name' => 'image-create']);
        Permission::create(['name' => 'image-read']);
        Permission::create(['name' => 'image-publish']);
        Permission::create(['name' => 'image-unpublish']);
        Permission::create(['name' => 'image-approve']);
        Permission::create(['name' => 'image-unapproved']);
        Permission::create(['name' => 'image-update']);
        Permission::create(['name' => 'image-destroy']);
        Permission::create(['name' => 'image-restore']);
        Permission::create(['name' => 'image-force-delete']);

        // rating permissions
        Permission::create(['name' => 'rating-registrations']);
        Permission::create(['name' => 'rating-create']);
        Permission::create(['name' => 'rating-read']);
        Permission::create(['name' => 'rating-publish']);
        Permission::create(['name' => 'rating-unpublish']);
        Permission::create(['name' => 'rating-approve']);
        Permission::create(['name' => 'rating-unapproved']);
        Permission::create(['name' => 'rating-update']);
        Permission::create(['name' => 'rating-destroy']);
        Permission::create(['name' => 'rating-restore']);
        Permission::create(['name' => 'rating-force-delete']);

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        /**************** create roles ********************/
        Role::create(['name' => 'super-admin'])->syncPermissions(Permission::all()); // my role only
        Role::create(['name' => 'editor'])->syncPermissions(Permission::all()); // secretary role
        Role::create(['name' => 'moderator'])->syncPermissions(['event-registrations', 'event-create', 'event-read', 'event-update', 'event-publish', 'event-unpublish', 'event-approve', 'event-unapproved', 'event-destroy', 'post-registrations', 'post-create', 'post-read',
            'post-update', 'post-publish', 'post-unpublish', 'post-approve', 'post-unapproved', 'article-registrations', 'article-read', 'story-registrations', 'story-create', 'story-read', 'story-update', 'story-publish', 'story-unpublish', 'story-approve', 'story-unapproved']); // moderator of posts
        Role::create(['name' => 'admin'])->syncPermissions(); // admin role
        Role::create(['name' => 'user'])->syncPermissions(['event-registrations', 'event-create', 'event-read', 'event-update', 'post-registrations', 'post-create', 'post-read',
            'post-update', 'article-registrations', 'article-read', 'story-registrations', 'story-create', 'story-read', 'story-update']); // user role
        Role::create(['name' => 'guest'])->syncPermissions('mail-send');
    }
}
