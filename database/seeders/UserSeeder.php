<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'mrbig00@gmail.com'],
            [
                'name' => 'Szanto Zoltan',
                'password' => 'password',
            ]
        );

        User::updateOrCreate(
            ['email' => 'hubamagyarosi@gmail.com'],
            [
                'name' => 'Magyarosi Huba',
                'password' => 'password',
            ]
        );
    }
}
