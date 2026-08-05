<?php

namespace App\Models\GDA;

use Illuminate\Database\Eloquent\Model;

class RelazioneRateGDA extends Model
{
    protected $connection = 'oracle_ugov';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siadg').'.V_IE_DG11_R_RATE_COMPENSO';
    }

    //id_dg_ref_a è il contratto
    //id_dg_ref_b è il compenso

    public function compenso()
    {
        return $this->hasOne(CompensoGDA::class, 'id_dg', 'id_dg_ref_b');
    }


}
