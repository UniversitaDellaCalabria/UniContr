<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitoloStudioUgov extends Model
{
    protected $connection = 'oracle';
    //protected $table = 'ANA_TIT_STUDIO';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siaru').'.V_IE_RU_PERS_TITSTU';
    }
}
