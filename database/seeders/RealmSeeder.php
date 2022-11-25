<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RealmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        //
        DB::table('realms')->insert([
            'id' => 61,
            'name' => "Zul'jin",
            'slug' => 'zuljin',
            'region' => 'US'
        ]);

    }
}
