<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('offices')->insertOrIgnore([
            ['code'=>'OPG',    'name'=>'Office of the Provincial Governor'],
            ['code'=>'OPA',    'name'=>'Office of the Provincial Administrator'],
            ['code'=>'PICTO',  'name'=>'Provincial Information and Communications Technology Office'],
            ['code'=>'PHRMDO', 'name'=>'Provincial Human Resource and Management Development Office'],
            ['code'=>'SP',     'name'=>'Office of the Sangguniang Panlalawigan'],
            ['code'=>'PGENRO', 'name'=>'Provincial Government Environment and Natural Resources Office'],
            ['code'=>'PGSO',   'name'=>'Provincial General Services Office'],
            ['code'=>'LUPTO',  'name'=>'La Union Provincial Tourism Office'],
            ['code'=>'PIO',    'name'=>'Provincial Information Office'],
            ['code'=>'PTO',    'name'=>'Provincial Treasury Office'],
            ['code'=>'OPAG',   'name'=>'Office of the Provincial Agriculturist'],
            ['code'=>'LEEIPO', 'name'=>'La Union Economic Enterprise and Investment Promotions Office'],
            ['code'=>'PPDC',   'name'=>'Provincial Planning and Development Coordinator'],
            ['code'=>'PBO',    'name'=>'Provincial Budget Office'],
            ['code'=>'PEO',    'name'=>'Provincial Engineering Office'],
            ['code'=>'OPAss',  'name'=>'Office of the Provincial Assessor'],
            ['code'=>'PSWDO',  'name'=>'Provincial Social Welfare and Development Office'],
            ['code'=>'LUPGEO', 'name'=>'La Union Provincial Government Engineering Office'],
            ['code'=>'OTHERS', 'name'=>'Others'],
        ]);
    }
}