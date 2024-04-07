<?php

namespace Database\Seeders;

use App\Models\Applicant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            [
                'first_name' => 'John', 
                'last_name' => 'Doe',
                'email' => 'johndoe@morepower.ph',
                'status' => 'New Applicant',
            ],[
                'first_name' => 'Max', 
                'last_name' => 'Pain',
                'email' => 'maxpain@morepower.ph',
                'status' => 'Application for Final Inteview',
            ],[
                'first_name' => 'Juan', 
                'last_name' => 'Dela Cruz',
                'email' => 'juandelacruz@morepower.ph',
                'status' => 'Application for Initial Interview',
            ],
        ];

        foreach ($defaults as $row) {
            $existingRecord = Applicant::where('email', $row['email'])->first();
    
            if (!$existingRecord) {
                $user = Applicant::create($row);
                // $user->assignRole('Admin');
            }
        }
    }
}
