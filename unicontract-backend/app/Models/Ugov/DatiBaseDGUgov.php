<?php

namespace App\Models\Ugov;

use Illuminate\Database\Eloquent\Model;

class DatiBaseDGUgov extends Model
{
    protected $connection = 'oracle';
    protected $table = 'SIADG_UNICAL_PROD.V_IE_DG02_DG';

}


