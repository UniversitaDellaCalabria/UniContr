<?php

namespace App\Models\GDA;

use Illuminate\Database\Eloquent\Model;

class CompensoGDA extends DGGDA
{
    protected $connection = 'oracle';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siadg').'.V_IE_DG15_X_COMPENSO';
    }


    protected $nome_tipo_dg = 'COMPENSO';

    static protected function getNomeTipoDgValue(){
        return  'COMPENSO';
    }

    public function relazionirate()
    {
        return $this->hasMany(RelazioneRateGDA::class, 'id_dg_ref_b', 'id_dg');
    }

    public function ordinativi()
    {
        return $this->hasManyThrough(
            PagamentoGDA::class,
            RelazioniDgGDA::class,
            'id_dg_1',
            'id_dg',
            'id_dg',
            'id_dg_2'
        );
    }
}


