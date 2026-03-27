<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InsegnamGDA;
use App\Precontrattuale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Auth;

class InsegnamGDAController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $anno)
    {
        $queryBuilder = InsegnamGDA::orderBy('DOC_COGNOME', 'ASC')->orderBy('DOC_NOME', 'ASC')->orderBy('COPER_ID', 'ASC')
            ->where([
                ['AA_OFF_ID', '=', $anno],
                ['DOC_RUOLO', '<>', 'PA'],
                ['DOC_RUOLO', '<>', 'PO'],
                ['DOC_RUOLO', '<>', 'RU'],
                ['MOTIVO_ATTO_COD', '<>', null]
            ]);
        $insegn = $queryBuilder->get();
        return response()->json([
            'lista' => $insegn,
            'success' => true
        ]);

    }

    public function show($coper_id)
    {
        $datiGDA = [];
        $message = '';

        $datiGDA = InsegnamGDA::join(config('unical.db_oracle_siaru').'.VD_ANAGRAFICA',
                                     config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DOC_MATRICOLA', '=', config('unical.db_oracle_siaru').'.VD_ANAGRAFICA.MATRICOLA')
                                     
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF',
                   config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.COPER_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS',
                   config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.MODULI_PDS_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS.MODULI_PDS_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT',
                   config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS.ANA_MOD_SETT_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.ANA_MOD_SETT_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_SETT',
                   config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.SETT_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_SETT.SETT_COD')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L2_UP2_DOCENTI',
                   config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DOC_MATRICOLA', '=', config('unical.db_oracle_gdaie').'.ODS_L2_UP2_DOCENTI.MATRICOLA')
                   
            ->where(config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', $coper_id)
            ->first([
                config('unical.db_oracle_siaru').'.VD_ANAGRAFICA.ID_AB',
                config('unical.db_oracle_siaru').'.VD_ANAGRAFICA.EMAIL',
                config('unical.db_oracle_siaru').'.VD_ANAGRAFICA.E_MAIL',
                config('unical.db_oracle_siaru').'.VD_ANAGRAFICA.E_MAIL_PRIVATA',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.*',
                config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.ANNO_CORSO',
                config('unical.db_oracle_gdaie').'.ODS_L1_SETT.SETT_COD',
                config('unical.db_oracle_gdaie').'.ODS_L1_SETT.SETT_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L2_UP2_DOCENTI.COD_FISC',
                config('unical.db_oracle_gdaie').'.ODS_L2_UP2_DOCENTI.GENDER_COD'
            ]);

        // GDA
        $atti = DB::connection('oracle')->table(config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI')

                ->join(config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO',
                   config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_ATTO_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO.TIPO_ATTO_COD')
                   
                ->join(config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE',
                   config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_EMITTENTE_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE.TIPO_EMITTENTE_COD')
                   
                ->where(config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.COPER_ID','=',$coper_id)
                ->where(function($query) {
                    //~ $query->where('tipo_atto_des','=','Delibera')
                          //~ ->orWhere('tipo_atto_des','=','Disposizione Direttore')
                          //~ ->orWhere('tipo_atto_des','=','Decreto Direttore');
                    $query->where(config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_ATTO_COD','=','DEL')
                          ->orWhere(config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_ATTO_COD','=','DD');
                          //~ ->orWhere('tipo_atto_des','=','Decreto Direttore');
                })
                //~ ->select('tipo_atto_des','tipo_emitt_des','motivo_atto_cod','numero','data')
                ->select(
                    config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_ATTO_COD',
                    config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO.TIPO_ATTO_DESC_ITA',
                    config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_EMITTENTE_COD',
                    config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE.TIPO_EMITTENTE_DESC_ITA',
                    config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.MOTIVO_ATTO_COD',
                    config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.NUMERO_PROVVEDIMENTO',
                    config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.DATA_PROVVEDIMENTO'
                )
                ->orderBy(config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.DATA_PROVVEDIMENTO', 'asc')
                ->get();

        $tipo_atto_des_string = "";
        $tipo_emitt_des_string = "";
        $motivo_atto_cod_string = "";
        $numero_string = "";
        $data_string = "";

        $counter = 0;

        foreach ($atti as $atto) {
            //~ $tipo_atto_des_string .= $atto->tipo_atto_des;
            $tipo_atto_des_string .= $atto->tipo_atto_desc_ita;
            //~ $tipo_emitt_des_string .= $atto->tipo_emitt_des;
            $tipo_emitt_des_string .= $atto->tipo_emittente_desc_ita;
            // $motivo_atto_cod_string .= $atto->motivo_atto_cod;
            $numero_string .= $atto->numero_provvedimento;
            $data_string .= $atto->data_provvedimento;

            if ( $counter == 0){
                $motivo_atto_cod_string = $atto->motivo_atto_cod;
            }

            if ( $counter < count( $atti ) - 1){
                $tipo_atto_des_string .= "#";
                $tipo_emitt_des_string .= "#";
                // $motivo_atto_cod_string .= "#";
                $numero_string .= "#";
                $data_string .= "#";
            }

            $counter = $counter + 1;
        }

        $datiGDA['tipo_atto_des'] = $tipo_atto_des_string;
        $datiGDA['tipo_emitt_des'] = $tipo_emitt_des_string;
        $datiGDA['motivo_atto_cod'] = $motivo_atto_cod_string;
        $datiGDA['numero'] = $numero_string;
        $datiGDA['data'] = $data_string;
        // end atti

        $ore_desc = DB::connection('oracle')->table(config('unical.db_oracle_gdaie').'.ODS_L1_ORE_COPER')
                    ->where('coper_id','=',$coper_id)
                    ->select('tipo_att_did_cod','ore','compenso_calcolato')
                    ->get();
        $ore_desc_string = "";
        $compenso_calcolato = 0;
        foreach ($ore_desc as $single_desc) {
            $ore_desc_string .="(";
            $ore_desc_string .=$single_desc->tipo_att_did_cod;
            $ore_desc_string .="-";
            $ore_desc_string .=$single_desc->ore;
            $ore_desc_string .=")";

            if($single_desc->compenso_calcolato != null)
                $compenso_calcolato += $single_desc->compenso_calcolato;
        }

        if($datiGDA['compenso'] == 0){
            $datiGDA['compenso'] = $compenso_calcolato;
        }
        $datiGDA['ore_desc'] = $ore_desc_string;

        $datiGDA['contatore_insegnamenti'] = InsegnamGDAController::contatoreInsegnamenti($coper_id);

        // PATCH per email istituzionale
        if($datiGDA['id_ab']) {
            $email = DB::connection('oracle')->table(config('unical.db_oracle_siaxm').'.V_IE_AC_PF_CONTATTI_ALL')
                    ->where('ID_AB','=',$datiGDA['id_ab'])
                    ->where('CD_TIPO_CONT','=','EMAIL')
                    ->orderBy('PRG_PRIORITA', 'desc')
                    ->get();
            if(isset($email[0]) && $email[0]->contatto){
                Log::info("Email istituzionale recuperata: ".$email[0]->contatto);
                $datiGDA['email'] = $email[0]->contatto;
            }
        }

        // PATCH - Cessazione anticipata
        if($datiGDA['data_rinuncia']) {
            $datiGDA['data_fine_contratto'] = explode(" ", $datiGDA['data_rinuncia'])[0];
        }

        $success = true;
        return compact('datiGDA', 'message', 'success');
    }

    //condizione necessaria che il contratto corrente si CONF_INC
    public static function contatoreInsegnamenti($coper_id, $force = true) {

        //se il motivo atto è un contratto di alta qualificazione ... $this->tipoContr == 'ALTQG' || $this->tipoContr == 'ALTQC' || $this->tipoContr == 'ALTQU';
        //allora vado a cercare il APPR_INC
        $count = 0;
        //leggere da gda insegnamento ...

        // GDA todo
        $insegnamentoGDA = InsegnamGDA::where(config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', $coper_id)
        
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF',
                   config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.COPER_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS',
                   config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.MODULI_PDS_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS.MODULI_PDS_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT',
                   config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS.ANA_MOD_SETT_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.ANA_MOD_SETT_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_SETT',
                   config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.SETT_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_SETT.SETT_COD')

            ->join(config('unical.db_oracle_gdaie').'.ODS_L2_UP2_DOCENTI',
                   config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DOC_MATRICOLA', '=', config('unical.db_oracle_gdaie').'.ODS_L2_UP2_DOCENTI.MATRICOLA')

            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI',
                   config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.COPER_ID')

            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO',
                   config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_ATTO_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO.TIPO_ATTO_COD')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE',
               config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_EMITTENTE_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE.TIPO_EMITTENTE_COD')
                   
            ->first([
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_COPER_COD',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_INIZIO_CONTRATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_FINE_CONTRATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.CFU', // GDA todo // COPER_PESO non c'è su gda MA DOVREBBE ESSERE CFU
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.ORE',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COMPENSO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.MOTIVO_ATTO_COD',
                config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO.TIPO_ATTO_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE.TIPO_EMITTENTE_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.NUMERO_ATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_ATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_PERIODO_DID_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L1_SETT.SETT_DESC_ITA',// GDA todo
                config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.SETT_COD', // GDA todo
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.AF_OFF_ID', // GDA todo // AF_RADICE_ID boh? // AF_OFF_ID??
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_CORSO_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.ANNO_CORSO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DOC_AFF_ORG',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DOC_AFF_ORG_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_RINUNCIA',
                config('unical.db_oracle_gdaie').'.ODS_L2_UP2_DOCENTI.COD_FISC',
                config('unical.db_oracle_gdaie').'.ODS_L2_UP2_DOCENTI.GENDER_COD'
            ]);

        $tipo_coper_cod = $insegnamentoGDA->tipo_coper_cod;

        $datiGDA = null;

        if ( $tipo_coper_cod == 'ALTQG' ||
             $tipo_coper_cod == 'ALTQC' ||
             $tipo_coper_cod == 'ALTQU' ||
             $tipo_coper_cod == 'TC004' ||
             $tipo_coper_cod == 'TC005' ||
             $tipo_coper_cod == 'TC006' ||
             $tipo_coper_cod == 'TC007'){
            //contratto di alta qualificazione
            //non c'è BAN_INC cerco il primo contratto APPR_INC
            $datiGDA = self::queryFirstMotivoAttoCod($coper_id, ['APPR_INC', 'PROP_INC']);

        }else{
            //altro
            $datiGDA = self::queryFirstMotivoAttoCod($coper_id, ['BAN_INC']);
        }

        //ATTENZIONE per i casi di rinnovi contratti già a sistema, di didattica ufficiale ma con affidamento incarico di docenza
        if (!$datiGDA){
            $datiGDA = self::queryFirstMotivoAttoCod($coper_id, ['APPR_INC', 'BAN_INC', 'PROP_INC']);
            Log::info('Conferimento incarico [ cod_coper_id: '.$coper_id.' ] tipo contratto: '.$tipo_coper_cod.' - non consistente con l\'origine dell\'attribuzione');
        }

        // GDA todo
        if ($datiGDA){
            $result = DB::connection('oracle')
                ->table(config('unical.db_oracle_gdaie').'.ODS_L2_COPER V1')
                ->join(config('unical.db_oracle_gdaie').'.ODS_L2_COPER V2', function($join) use($coper_id){
                $join->on('V2.ANA_AF_COD', '=', 'V1.ANA_AF_COD')
                     ->on(DB::raw("COALESCE(V2.SEDE_ID, 1)"), '=', DB::raw("COALESCE(V1.SEDE_ID, 1)"))
                     ->on(DB::raw("COALESCE(V2.PART_STU_ID,-1)"), '=', DB::raw("COALESCE(V1.PART_STU_ID,-1)"))
                     ->on('V2.DOC_ID_AB','=','V1.DOC_ID_AB'); // GDA todo // manca il "cod_fis"
            })->where('V1.COPER_ID','=',$coper_id)->where('V2.data_inizio_contratto','<',$datiGDA->data_contratto_corrente)
            ->where('V2.data_inizio_contratto','>=',$datiGDA->ultima_nuova_attribuzione)
            ->distinct()
            ->select(
                'V1.coper_id',
                'V2.motivo_atto_cod',
                'V2.aa_off_id', // GDA todo // boh?
                'V2.data_inizio_att_dida',
                'V2.ana_af_cod',
                'V1.sede_id',
                'V2.sede_id',
                'V1.PART_STU_ID',
                'V2.PART_STU_ID',
                'V2.data_inizio_contratto',
                'V2.data_fine_contratto'
            )
            ->get();
            $count = $result->count();
        }else{
            Log::info('Conferimento incarico [ cod_coper_id: '.$coper_id.' ] senza BAN_INC o APPR_INC o PROP_INC');
            if ($force){
                //NON c'è il BAN_INC o APPR_INC conto tutti i contratti CONF_INC PRESENTI escludendo il presente
                //è un caso di errore quindi ritorno 0
                //è impostato un rinnovo ma non vengono trovati i dati per il rinnovo
                $count = DB::connection('oracle')
                    ->table(config('unical.db_oracle_gdaie').'.ODS_L2_COPER V1')
                    ->join(config('unical.db_oracle_gdaie').'.ODS_L2_COPER V2', function($join) use($coper_id){
                    $join->on('V2.ANA_AF_COD', '=', 'V1.ANA_AF_COD')
                        ->on(DB::raw("COALESCE(V2.SEDE_ID, 1)"), '=', DB::raw("COALESCE(V1.SEDE_ID, 1)"))
                        ->on(DB::raw("COALESCE(V2.PART_STU_ID,-1)"), '=', DB::raw("COALESCE(V1.PART_STU_ID,-1)"))
                        ->on('V2.DOC_ID_AB','=','V1.DOC_ID_AB') // GDA todo // manca il "cod_fis"
                        ->on('V2.data_inizio_contratto','<','V1.data_inizio_contratto');
                })->where('V1.COPER_ID','=',$coper_id)->where('V2.motivo_atto_cod','=','CONF_INC')->count();
                return $count;
            }
        }

        return $count;
    }

    public static function queryFirstMotivoAttoCod($coper_id,$motivo_atto_cod_array)
    {
        $datiGDA = DB::connection('oracle')->table(config('unical.db_oracle_gdaie').'.ODS_L2_COPER V1')->join(config('unical.db_oracle_gdaie').'.ODS_L2_COPER V2', function($join) use($coper_id) {
            $join->on('V2.ANA_AF_COD', '=', 'V1.ANA_AF_COD')
                 ->on(DB::raw("COALESCE(V2.SEDE_ID, 1)"), '=', DB::raw("COALESCE(V1.SEDE_ID, 1)"))
                 ->on(DB::raw("COALESCE(V2.PART_STU_ID,-1)"), '=', DB::raw("COALESCE(V1.PART_STU_ID,-1)"))
                 ->on('V2.DOC_ID_AB','=','V1.DOC_ID_AB') // GDA todo // manca il "cod_fis"
                 ->on('V2.data_inizio_contratto','<','V1.data_inizio_contratto');
        })
        ->whereIn('V2.motivo_atto_cod',$motivo_atto_cod_array)
        ->where('V1.COPER_ID','=', $coper_id)->where('V1.motivo_atto_cod','=','CONF_INC')
        ->select('V2.data_inizio_contratto as ultima_nuova_attribuzione','V1.data_inizio_contratto as data_contratto_corrente','V2.motivo_atto_cod as motivo_atto_cod_inizio')
        ->orderBy('V2.data_inizio_contratto', 'DESC')->first();

        return $datiGDA;
    }

    public function query(Request $request){

        $app = $request->json();
        $parameters = $request->json()->all();
        $parameters['order_by'] = 'doc_cognome,ASC|doc_nome,ASC';
        array_push($parameters['rules'],[
            "operator" => "NotIn",
            "field" => "DOC_RUOLO",
            "value" => ['PA','PO','RU']
        ]);
        array_push($parameters['rules'],[
            "operator" => "!=",
            "field" => "DOC_MATRICOLA",
            "value" => '[null]'
        ]);

        array_push($parameters['rules'],[
            "operator" => "!=",
            "field" => "DATA_INIZIO_CONTRATTO",
            "value" => '[null]'
        ]);
        //filtro insegnamenti con rinuncia
        array_push($parameters['rules'],[
            "operator" => "NotIn",
            "field" => "STATO_COPER_COD",
            "value" => ['R', 'X']
        ]);


        //Ricercare tutte le precontrattuali dell'anno di ricerca
        $collection = collect($parameters['rules']);

        //filtrare gli insegnamenti già importati
        $rule = $collection->first(function ($value, $key) {
            return $value['field'] == 'AA_OFF_ID';
        });

        if ($rule){
            $precontrs = Precontrattuale::with('insegnamento')->whereHas('insegnamento',function($query) use($rule){
                $query->where('aa', (int)$rule['value']);
                $query->select('aa','coper_id','id');
            })->where('stato','<',2)->get();

        }else{
            $precontrs = Precontrattuale::with(['insegnamento'])->where('stato','<',2)->get();
        }

        if ($precontrs){
            $coper_ids = $precontrs->pluck('insegnamento.coper_id')->toArray();
            array_push($parameters['rules'],[
                "operator" => "NotIn",
                "field" => "COPER_ID",
                "value" => $coper_ids
            ]);
        }


        // se l'utente NON ha il permesso di ricerca su tutti i contratti
        if (!Auth::user()->hasPermissionTo('search all insegnamenti')){

            //aggiungere filtro per unitaorganizzativa_uo
            $uo = Auth::user()->unitaorganizzativa();
            $sede = Auth::user()->sede();

            if ($uo == null && $sede == null) {
                abort(403, trans('global.utente_non_autorizzato'));
            }

            // check uo
            if ($sede == null) {
                if ($uo->isPlesso()){
                    //filtro per unitaorganizzativa dell'utente di inserimento (plesso)
                    array_push($parameters['rules'],[
                        "operator" => "In",
                        //"field" => "dip_cod",
                        "field" => "DOC_AFF_ORG",
                        "value" => $uo->dipartimenti()
                    ]);
                } else {
                    //ad un afferente al dipartimento filtro per dipartimento
                    array_push($parameters['rules'],[
                        "operator" => "=",
                        //"field" => "dip_cod",
                        "field" => "DOC_AFF_ORG",
                        "value" => $uo->uo
                    ]);
                }
            }

            // check sede
            else if ($uo == null) {
                if ($sede->isPlesso()){
                    //filtro per unitaorganizzativa dell'utente di inserimento (plesso)
                    array_push($parameters['rules'],[
                        "operator" => "In",
                        //"field" => "dip_cod",
                        "field" => "DOC_AFF_ORG",
                        "value" => $sede->dipartimenti()
                    ]);
                } else {
                    //ad un afferente al dipartimento filtro per dipartimento
                    array_push($parameters['rules'],[
                        "operator" => "=",
                        //"field" => "dip_cod",
                        "field" => "DOC_AFF_ORG",
                        "value" => $sede->uo
                    ]);
                }
            }

            // check entrambi
            else {
                if ($uo->isPlesso()) $lista = $uo->dipartimenti();
                else $lista = array($uo->uo);

                if ($sede->isPlesso()) $lista = array_merge($lista, $sede->dipartimenti());
                else array_push($lista, $sede->uo);

                array_push($parameters['rules'],[
                    "operator" => "In",
                    //"field" => "dip_cod",
                    "field" => "DOC_AFF_ORG",
                    "value" => $lista
                ]);
            }
        }


        $findparam =new \App\FindParameter($parameters);

        $queryBuilder = new QueryBuilderForceInsensitive(new InsegnamGDA, $request, $findparam);

        return $queryBuilder->build()->paginate();
    }


}
