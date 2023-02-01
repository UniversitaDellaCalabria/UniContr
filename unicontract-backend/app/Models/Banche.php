<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banche extends Model
{
    protected $connection = 'oracle';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siaxm').'.V_IE_AC_BANCHE';
    }
}
