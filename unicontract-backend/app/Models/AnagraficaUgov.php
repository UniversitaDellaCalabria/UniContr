<?php

// GDA OK

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RappParentelaUgov;
//~ use App\Models\InsegnamUgov;
use App\Models\InsegnamGDA;

class AnagraficaUgov extends Model
{
    protected $connection = 'oracle';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siaru').'.VD_ANAGRAFICA';
    }

    public $primaryKey = 'ID_AB';

    protected $casts = [
        'data_nasc' => 'datetime:d-m-Y',
    ];

    public function ugovIns() {
        return $this->hasMany(InsegnamGDA:: class, 'MATRICOLA', 'MATRICOLA');
    }

    // restituisce un persona cercandola dalla sua email
    public function scopeFindByEmail($query, $email)
    {
        return $query->where('email',$email)->first();
    }
}
