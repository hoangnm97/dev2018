<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use YaroslavMolchan\Rbac\Models\Permission;
use YaroslavMolchan\Rbac\Models\Role;

class RbacRoleGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:role-generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rbac generate role and permission';

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
        $roleInits = config('rbac.roles');

        foreach ($roleInits as $slug => $role){
            Role::create([
                'name' => $role,
                'slug' => $slug
            ]);
            $this->line('Created role:  ======> ' . $slug . ' ===> ' . $role);
        }

    }
}
