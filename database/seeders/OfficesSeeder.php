<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('offices')->insert([
            ['code'=>'PICTO','name'=>'Provincial ICT Office'],
            ['code'=>'OPG','name'=>'Office of the Provincial Governor'],
            ['code'=>'OPA','name'=>'Office of the Provincial Administrator'],
            ['code'=>'PHRMDO','name'=>'HR Management Office'],
            ['code'=>'SP','name'=>'Sangguniang Panlalawigan'],
            ['code'=>'PGENRO','name'=>'Environment Office'],
            ['code'=>'PGSO','name'=>'General Services Office'],
            ['code'=>'LUPTO','name'=>'Tourism Office'],
            ['code'=>'PIO','name'=>'Information Office'],
            ['code'=>'PTO','name'=>'Treasury Office'],
            ['code'=>'OPAG','name'=>'Agriculturist Office'],
            ['code'=>'LEEIPO','name'=>'Economic Enterprise Office'],
            ['code'=>'PPDC','name'=>'Planning Office'],
            ['code'=>'PBO','name'=>'Budget Office'],
            ['code'=>'PEO','name'=>'Engineering Office'],
            ['code'=>'OPAss','name'=>'Assessor Office'],
            ['code'=>'PSWDO','name'=>'Social Welfare Office'],
            ['code'=>'LUPGEO','name'=>'Other Offices'],
        ]);
    }
}