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
        $roleCount = Role::all()->count();
        
        if($roleCount > 0 ){
            $this->command->line("Roles are already seeded");
        }else{
            $roles=[
                "Super User",
                "Comelec Officer",
                "Student",
                "Registrar Officer"
            ];
            foreach ($roles as $key => $value) {
                factory(Role::class)->create([
                    "name" => $value
                ]);
            }
        }
        
        
    }
}
