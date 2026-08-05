<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatoCivileGDA extends Model
{
    protected $connection = 'oracle_ugov';    
    protected $table = 'STATO_CIVILE';
}
