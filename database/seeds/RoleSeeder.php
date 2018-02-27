<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            "Super User" => "App\Admin",
            "Comelec Officer" => "App\Comelec",
            "Student" => "App\Student",
            "Registrar Officer" => "App\Registrar"
        ];
        $roleCount = Role::all()->count();

        if ($roleCount > 0) {
            $this->command->line("Roles are already seeded");
            if ($this->command->confirm('Update Roles with Models?')) {
                foreach ($roles as $key => $value) {
                    $role = Role::where('name', $key)->first();
                    $role->model = $value;
                    $role->save();
                }
                $this->command->line('Update Complete');
            }
        } else {
            foreach ($roles as $key => $value) {
                factory(Role::class)->create([
                    "name" => $key,
                    "model" => $value
                ]);
            }
        }


    }
}
