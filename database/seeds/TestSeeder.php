<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Admin;
use App\User;
class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if($this->command->confirm("This is a user seed, continue?")){

            //get existing admin
            $getRoleId= Role::where("name","Super User")->first();
            $checkExistingAdmin = User::where("role_id",$getRoleId->id)->get();
            $count = $checkExistingAdmin->count();
             

            if($count >= 1){
                $this->command->line("User already existed");
                $this->command->line("number of Users {$count}");
                $getExistingAdmin = $checkExistingAdmin[0];
                $this->command->line(" User ID: { $getExistingAdmin->id } Email: { $getExistingAdmin->email } ");


                if($this->command->confirm("Would you like to override this account for new email and password?")){
                    $email = $this->command->ask("New Email");
                    $password = $this->command->ask("New Password");

                    $updateAdmin = User::find($getExistingAdmin->id);
                    $updateAdmin->email = $email;
                    $updateAdmin->password = bcrypt($password);
                    $updateAdmin->save();

                    $this->command->line("You may now login using the latest administrator credentials. ");
                }

            }else{


                $email = $this->command->ask("Enter you email address. Please do not leave blank ");
                $this->command->line("You entered {$email}");
                $password = $this->command->ask("Enter you password. Please do not leave blank ");
                $this->command->line("Your password is set please proceed.");
    
    
    
                $first_name =ucwords($this->command->ask("First Name"));
                $middle_name =ucwords($this->command->ask("Middle Name"));
                $last_name =ucwords($this->command->ask("Last Name"));
    
    
    
                //instantiate user
                $user = new User();
                $user->name = $first_name." ".$last_name;
                $user->email = $email;
                $user->password = bcrypt($password) ;
                
                
                
                $adminRole = Role::find(1);
                //save user with attachment
                $adminRole->user()->save($user);
             
    
    
    
                //instantiate admin profile
                $admin = new Admin();
                $admin->first_name = $first_name;
                $admin->middle_name = $middle_name;
                $admin->last_name = $last_name;
                $admin->email = $email;
    
    
    
    
                //save admin profile with user relationship attached
                $user->adminProfile()->save($admin);
    
                $this->command->line("A new Super User is successfully created! You may now visit your dashboard to complete your information.");

            }
           

        }
    }
}
