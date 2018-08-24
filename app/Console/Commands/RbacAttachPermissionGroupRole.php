<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use YaroslavMolchan\Rbac\Models\PermissionGroup;
use YaroslavMolchan\Rbac\Models\Role;

class RbacAttachPermissionGroupRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:attachGroup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'attach permission group to role ';

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


        $rolePermissionGroups = config('rbac.rolePermissionGroups');
        foreach ($rolePermissionGroups as $role => $permissionGroup){
            $roleEl = Role::where('name', $role)->first();
            $permissionGroupEl = PermissionGroup::where('name', $permissionGroup)->first();

            if($roleEl && $permissionGroupEl){
                $roleEl->attachGroup($permissionGroupEl);

                $this->line('attachGroup: ' . $role . ' => ' . $permissionGroup);
            }

        }
    }
}
