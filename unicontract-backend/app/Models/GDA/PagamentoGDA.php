<?php

namespace App\Models\GDA;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ugov\RelazioniDgGDA;

class PagamentoGDA extends DGGDA
{
    protected $connection = 'oracle_ugov';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siadg').'.V_IE_DG19_X_ORDINATIVO';
    }

    protected $nome_tipo_dg = 'ORDINATIVO_PAGAMENTO_INCASSO';

    static protected function getNomeTipoDgValue(){
        return 'ORDINATIVO_PAGAMENTO_INCASSO';
    }

}


