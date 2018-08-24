<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use YaroslavMolchan\Rbac\Models\PermissionGroup;
use YaroslavMolchan\Rbac\Models\Role;

class RbacAttachUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rbac:attachRole';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'attach role to user';

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

        $user_ids = [1];
        $users = User::whereIn('id', $user_ids)
            ->get();

        $role = Role::find(1);

        if(!$role){
            $this->line('Role do not exits!');
            return;
        }

        if(count($users) > 0 && $role){
            foreach ($users as $user){
                $user->attachRole($role);
                $this->line('Added role '. $role->name .' to user ' . $user->name);
            }
        } else {
            $this->line('Not found user!');
        }

    }
}
