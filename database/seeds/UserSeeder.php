<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        //admin
        factory(User::class, 2)->create()->each(function ($u) {
            $superUser = App\Role::where("name", "Super User")->get();
            //$superUser->role()->save($u);
            $u->role_id = $superUser[0]->id;
            $u->save();
        });
        //student
        factory(User::class, 2)->create()->each(function ($u) {
            $student = App\Role::where("name", "Student")->get();
            //$superUser->role()->save($u);
            $u->role_id = $student[0]->id;
            $u->save();
        });
    }
}
