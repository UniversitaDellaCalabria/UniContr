<?php

/** GDA upgrade */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Models\InsegnamSegmentiUgov;

class InsegnamGDA extends Model
{
    protected $connection = 'oracle';

    public $table;
    public function __construct()
    {
       $this->table = config('unical.db_oracle_gdaie').'.ODS_L2_COPER';
    }

    // GDA join per recuperare dati non più presenti nella vista della copertura
    //~ protected static function booted()
    //~ {
        //~ static::addGlobalScope('gdaJoin', function ($builder) {
            //~ $db = config('unical.db_oracle_gdaie') . '.';

            //~ $builder->select('ODS_L2_COPER.*') // Prendi tutto dalla principale
                // Aggiungi solo le colonne che ti servono dalle altre 5 tabelle
                //~ ->addSelect([
                    //'t2.COD_FISC',
                    //~ 't3.descrizione_settore',
                    //~ 't4.nome_polo',
                    //~ 't5.codice_facolta',
                    //~ 't6.stato_insegnamento'
                //~ ])
                // Esegui le 5 Join
                //~ ->leftJoin($db . 'ODS_L2_UP2_DOCENTI as t2', 'ODS_L2_COPER.DOC_MATRICOLA', '=', 't2.MATRICOLA');
                //~ ->leftJoin($db . 'TABELLA_SETTORI as t3', 'ODS_L2_COPER.id_settore', '=', 't3.id')
                //~ ->leftJoin($db . 'TABELLA_POLI as t4', 'ODS_L2_COPER.id_polo', '=', 't4.id')
                //~ ->leftJoin($db . 'TABELLA_FACOLTA as t5', 'ODS_L2_COPER.id_facolta', '=', 't5.id')
                //~ ->leftJoin($db . 'TABELLA_STATI as t6', 'ODS_L2_COPER.id_stato', '=', 't6.id');
        //~ });
    //~ }
    
    protected $casts = [
        'data_inizio_contratto' => 'date:d-m-Y',
        'data_fine_contratto' => 'date:d-m-Y',
    ];

    //~ protected $appends = ['nominativo'];

    //~ public function anagrafica() {
        //~ return $this->hasOne(AnagraficaUgov::class, 'MATRICOLA', 'MATRICOLA');
    //~ }

    //~ public function getNominativoAttribute($input)
    //~ {
        //~ return $this->cognome.' '.$this->nome;
    //~ }

    //~ public function segmenti() {
        //~ return $this->hasMany(InsegnamSegmentiUgov::class, 'af_radice_id', 'af_radice_id')->Seg();
    //~ }

    //~ public function getSettDesAttribute($value){
        //~ if ($value==null){
            //~ if ($this->segmenti() != null && $this->segmenti()->count()>0){
                //~ return implode('; ', $this->segmenti->pluck('sett_des')->toArray());
            //~ }
        //~ }
        //~ return $value;
    //~ }

    //~ public function getSettCodAttribute($value){
        //~ if ($value==null){
            //~ if ($this->segmenti() != null && $this->segmenti()->count()>0){
                //~ return implode('; ', $this->segmenti->pluck('sett_cod')->toArray());
            //~ }
        //~ }
        //~ return $value;
    //~ }

}
