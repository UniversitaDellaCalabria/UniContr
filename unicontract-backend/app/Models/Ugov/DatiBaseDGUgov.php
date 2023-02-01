<?php

namespace App\Models\Ugov;

use Illuminate\Database\Eloquent\Model;

class DatiBaseDGUgov extends Model
{
    protected $connection = 'oracle';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siadg').'.V_IE_DG02_DG';
    }

}


