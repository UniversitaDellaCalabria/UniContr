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

    }
}
