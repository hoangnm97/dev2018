<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use YaroslavMolchan\Rbac\Models\Permission;

class RbacPermissionGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:permission-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'rbac permission generate';

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

        $permissionInits = config('rbac.permissions');


        foreach ($permissionInits as $slug => $permission){

            $exitPerms = Permission::where('name', $permission)
                ->where('slug', $slug)
                ->first();

            if(!$exitPerms){
                Permission::create([
                    'name' => $permission,
                    'slug' => $slug
                ]);
                $this->line('Created permission:  ======> ' . $slug . ' ===> ' . $permission);

            } else {
                $this->line('Exited permission:  ======> ' . $slug . ' ===> ' . $permission);
            }
        }

    }
}
