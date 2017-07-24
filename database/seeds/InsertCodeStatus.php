<?php

use Illuminate\Database\Seeder;

class InsertCodeStatus extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       	\App\StatusCode::create([
            'code'=> 'SB',
            'code_description' => 'Submitted'
        ]);

        \App\StatusCode::create([
            'code'=> 'RJ:WR3',
            'code_description' => 'Rejected WR3',
        ]);

        \App\StatusCode::create([
            'code'=> 'RJ:LP',
            'code_description' => 'Rejected LP',
        ]);

        \App\StatusCode::create([
            'code'=> 'AC:WR3',
            'code_description' => 'Accepted WR3',
        ]);

        \App\StatusCode::create([
            'code'=> 'AC:LP',
            'code_description' => 'Accepted LP',
        ]);

        \App\StatusCode::create([
            'code'=> 'UP',
            'code_description' => 'Updated',
        ]);
    }
}
