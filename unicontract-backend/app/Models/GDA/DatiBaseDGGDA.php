<?php

namespace App\Models\GDA;

use Illuminate\Database\Eloquent\Model;

class DatiBaseDGGDA extends Model
{
    protected $connection = 'oracle_ugov';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siadg').'.V_IE_DG02_DG';
    }

}


