<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use YaroslavMolchan\Rbac\Models\Permission;
use YaroslavMolchan\Rbac\Models\PermissionGroup;

class RbacPermissionGroupGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:permission-group';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create permission group';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $permissionGroups = config('rbac.permissionGroups');

        foreach ($permissionGroups as $permissionGroup){

            $group = PermissionGroup::where('name', $permissionGroup['name'])
                ->where('module', $permissionGroup['module'])
                ->first();

            if(!$group){
                $group = PermissionGroup::create([
                    'name' => $permissionGroup['name'],
                    'module' => $permissionGroup['module']
                ]);
            }

            $this->line('Group: ' . $permissionGroup['name'] . ' created.');

            foreach ($permissionGroup['permissions'] as $permission){

                $selectPermission = Permission::where('slug', $permission)->first();

                if($selectPermission){
                    $group->attachPermission($selectPermission->id);
                    $this->line('added ' . $permission . ' to ' . $permissionGroup['name']);
                } else {
                    $this->line('This permission: ' . $permission .' not exit ');
                }

            }

        }

    }
}
