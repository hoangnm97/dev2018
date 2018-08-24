<?php


use Illuminate\Support\Facades\DB;
use Carbon\Carbon as Carbon;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        if(env('DB_CONNECTION') == 'mysql')
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        if(env('DB_CONNECTION') == 'mysql')
            DB::table(config('auth.table'))->truncate();
        else //For PostgreSQL or anything else
            DB::statement("TRUNCATE TABLE ".config('auth.table')." CASCADE");

        //Add the master administrator, user id of 1
        $users = [
            [
                'name' => 'Badosa Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('badosa123456'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::table(config('auth.table'))->insert($users);

        if(env('DB_CONNECTION') == 'mysql')
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
