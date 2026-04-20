<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void {
        Group::insert([
            ['name' => 'manager'],
            ['name' => 'staff'],
        ]);

        User::create([
            'name' => 'Manager',
            'username' => 'manager',
            'email' => 'manager@fyrn.my.id',
            'password' => Hash::make('manager'),
        ])->groups()->attach(Group::where('name', 'manager')->value('id'));
        
        User::create([
            'name' => 'Staff',
            'username' => 'staff',
            'email' => 'staff@fyrn.my.id',
            'password' => Hash::make('staff'),
        ])->groups()->attach(Group::where('name', 'staff')->value('id'));

        ChartOfAccount::create([
            'name' => 'Saldo Penyeimbang',
            'type' => 'equity',
            'description' => 'Saldo Penyeimbang digunakan untuk menyeimbangkan modal awal yang ditambahkan' // Diperbaiki
        ]);
    }
}
