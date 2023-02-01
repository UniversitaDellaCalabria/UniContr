<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoTitoloStudio extends Model
{

    protected $connection = 'oracle';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siaru').'.TIPI_TITOLO_STUDIO';
    }
    public $primaryKey = 'TIPO_TITOLO';


}
