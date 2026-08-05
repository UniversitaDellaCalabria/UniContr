<?php

namespace App\Models\GDA;

use Illuminate\Database\Eloquent\Model;

class RelazioniDgGDA extends Model
{
    protected $connection = 'oracle_ugov';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siadg').'.V_IE_DG02_R_DG';
    }


    public function scopeDistinti($query){
        $query->select(['id_dg','id_dg_1','id_dg_2','dt_annullamento'])->distinct();
    }


    public function compenso()
    {
        return $this->belongsTo(CompensoGDA::class, 'ID_DG_2', 'ID_DG');
    }
}
