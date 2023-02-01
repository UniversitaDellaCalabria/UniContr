<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Organico extends Model
{
    protected $connection = 'oracle';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_siaxm').'.V_IE_RU_ORGANICO_COP';
    }

    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('Fetch', function ($builder) {
            $builder->select([config('unical.db_oracle_siaxm').'.V_IE_RU_ORGANICO_COP.matricola',config('unical.db_oracle_siaxm').'.V_IE_RU_ORGANICO_COP.nome',config('unical.db_oracle_siaxm').'.V_IE_RU_ORGANICO_COP.cognome', config('unical.db_oracle_siaxm').'.V_IE_RU_ORGANICO_COP.nome_esteso', config('unical.db_oracle_siaxm').'.V_IE_RU_ORGANICO_COP.cd_posizorg',
            config('unical.db_oracle_siaxm').'.V_IE_RU_ORGANICO_COP.ds_tipo_posizorg',config('unical.db_oracle_siaxm').'.V_IE_RU_ORGANICO_COP.id_ab_uo']);
        });
    }

    public function scopeRespArea($query, $funz = 'RESP_AREA')
    {
        return $query->where('cd_posizorg',$funz);
    }

    public function scopeValido($query)
    {
        return $query->where('dt_fine_cop', '>=',  Carbon::now());
    }

    public  function scopePersonaleUfficioAfferenza($query, $value_uo)
    {
        return $query->where('cd_csa', $value_uo);
    }

}
