<?php

use Illuminate\Database\Seeder;

class SeedMasterData extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Auth::create([
            'type'        => 'SU',
            'description' => 'Super User',
            'created_by'  => 'seeder'
        ]);
        \App\Auth::create([
            'type'        => 'OPEL',
            'description' => 'Operator Lembaga Penelitian',
            'created_by'  => 'seeder'
        ]);
        \App\Auth::create([
            'type'        => 'OWR3',
            'description' => 'Operator WR3',
            'created_by'  => 'seeder'
        ]);
        \App\Auth::create([
            'type'        => 'D',
            'description' => 'Dosen',
            'created_by'  => 'seeder'
        ]);
    }
}
