<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AnagraficaGDA;

class RappParentelaGDA extends Model
{
    protected $connection = 'oracle_ugov';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siaru').'.FAM_ANAGRAFICA';
    }
}
