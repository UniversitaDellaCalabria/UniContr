<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TipoTitoloStudio extends Model
{

    protected $connection = 'oracle';

    public $table = 'SIARU_UNICAL_PROD.TIPI_TITOLO_STUDIO';
    public $primaryKey = 'TIPO_TITOLO';


}
