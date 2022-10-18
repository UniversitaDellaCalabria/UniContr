<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $connection = 'oracle';
    protected $table = 'SIARU_UNICAL_PROD.COMUNE_PROV';
}
