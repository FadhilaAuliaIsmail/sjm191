<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Pemilik Usaha',
            'email' => 'pemilik@pos.test',
            'password' => bcrypt('password'),
            'peran' => 'pemilik_usaha',
        ]);

        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir@pos.test',
            'password' => bcrypt('password'),
            'peran' => 'kasir',
        ]);
    }
}
