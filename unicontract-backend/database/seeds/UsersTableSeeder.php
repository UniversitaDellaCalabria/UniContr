<?php

use Illuminate\Database\Seeder;
use App\User as User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'francesco.filicetti@unical.it')->first();
        if ($user==null) {
            $user = User::firstOrCreate( [
                'email' => 'francesco.filicetti@unical.it' ,
                'password' => Hash::make( 'test' ) ,
                'name' => 'Francesco Filicetti' ,
                'v_ie_ru_personale_id_ab'=> 48908,
                'cf' => 'ffffff00f00f000f',
                'nome' => 'Francesco',
                'cognome' => 'Filicetti'
            ] );
        }
        if (!$user->hasRole('super-admin')){
            $user->assignRole('super-admin');
        }

        $user = User::where('email', 'giuseppe.rossi@unical.it')->first();
        if ($user==null) {
            $user = User::firstOrCreate( [
                'email' => 'giuseppe.rossi@unical.it' ,
                'password' => Hash::make( 'testrossi' ) ,
                'name' => 'Giuseppe Rossi' ,
                'v_ie_ru_personale_id_ab'=> 5226,
                'cf' => 'rrrrrr00r00r000r',
                'nome' => 'Giuseppe',
                'cognome' => 'Rossi'
            ] );
        }
        if (!$user->hasRole('super-admin')){
            $user->assignRole('super-admin');
        }

        $user = User::where('email', 'paola.cordiale@unical.it')->first();
        if ($user==null) {
            $user = User::firstOrCreate( [
                'email' => 'paola.cordiale@unical.it' ,
                'password' => Hash::make( 'testcordiale' ) ,
                'name' => 'Paola Cordiale' ,
                'v_ie_ru_personale_id_ab'=> 5036,
                'cf' => 'cccccc00c00c000c',
                'nome' => 'Paola',
                'cognome' => 'Cordiale'
            ] );
        }
        if (!$user->hasRole('op_approvazione_economica')){
            $user->assignRole('op_approvazione_economica');
        }

        $user = User::where('email', 'daniela.pisani@unical.it')->first();
        if ($user==null) {
            $user = User::firstOrCreate( [
                'email' => 'daniela.pisani@unical.it' ,
                'password' => Hash::make( 'testpisani' ) ,
                'name' => 'Daniela Pisani' ,
                'v_ie_ru_personale_id_ab'=> 6544,
                'cf' => 'pppppp00p00p000p',
                'nome' => 'Daniela',
                'cognome' => 'Pisani'
            ] );
        }
        if (!$user->hasRole('op_approvazione_amm')){
            $user->assignRole('op_approvazione_amm');
        }

        $user = User::where('email', 'egidio.cario@unical.it')->first();
        if ($user==null) {
            $user = User::firstOrCreate( [
                'email' => 'egidio.cario@unical.it' ,
                'password' => Hash::make( 'testcario' ) ,
                'name' => 'Egidio Cario' ,
                'v_ie_ru_personale_id_ab'=> 5686,
                'cf' => 'eeeeee00e00e000e',
                'nome' => 'Egidio',
                'cognome' => 'Cario'
            ] );
        }
        if (!$user->hasRole('op_approvazione_amm')){
            $user->assignRole('op_approvazione_amm');
        }

        $user = User::where('email', 'silvia.pagano@unical.it')->first();
        if ($user==null) {
            $user = User::firstOrCreate( [
                'email' => 'silvia.pagano@unical.it' ,
                'password' => Hash::make( 'testpag' ) ,
                'name' => 'Silvia Pagano' ,
                'v_ie_ru_personale_id_ab'=> 9603,
                'cf' => 'ssssss00e00e000e',
                'nome' => 'Silvia',
                'cognome' => 'Pagano'
            ] );
        }
        if (!$user->hasRole('op_approvazione_amm')){
            $user->assignRole('op_approvazione_amm');
        }

    }
}
