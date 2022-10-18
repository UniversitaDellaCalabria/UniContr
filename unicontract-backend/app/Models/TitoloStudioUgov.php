<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitoloStudioUgov extends Model
{
    protected $connection = 'oracle';
    //protected $table = 'ANA_TIT_STUDIO';
    protected $table = 'SIAXM_UNICAL_PROD.V_IE_RU_PERS_TITSTU';
}
