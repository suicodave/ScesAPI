<?php

use Illuminate\Database\Seeder;
use App\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $roleCount = Department::all()->count();
        
        if($roleCount > 0 ){
            $this->command->line("Departments are already seeded");
        }else{
            $departments = [
                'Elementary',
                'Junior High',
                'Senior High',
                'College'
            ];
            foreach ($departments as $key => $value) {
                factory(Department::class)->create([
                    "name" => $value
                ]);
            }
        }
    }
}
