<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AnagraficaUgov;

class RappParentelaUgov extends Model
{
    protected $connection = 'oracle';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siaru').'.FAM_ANAGRAFICA';
    }
}
