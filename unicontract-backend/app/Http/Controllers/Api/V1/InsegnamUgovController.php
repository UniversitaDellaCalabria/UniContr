<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\InsegnamUgov;
use App\Precontrattuale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Auth;
class InsegnamUgovController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $anno)
    {
        $queryBuilder = InsegnamUgov::orderBy('COGNOME', 'ASC')->orderBy('NOME', 'ASC')->orderBy('COPER_ID', 'ASC')
            ->where([
                ['AA_OFF_ID', '=', $anno],
                ['RUOLO_DOC_COD', '<>', 'PA'],
                ['RUOLO_DOC_COD', '<>', 'PO'],
                ['RUOLO_DOC_COD', '<>', 'RU'],
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
        $datiUgov = [];
        $message = '';

        $datiUgov = InsegnamUgov::join('SIARU_UNICAL_PROD.VD_ANAGRAFICA', 'SIAXM_UNICAL_PROD.V_IE_DI_COPER.MATRICOLA', '=', 'SIARU_UNICAL_PROD.VD_ANAGRAFICA.MATRICOLA')
            ->where('SIAXM_UNICAL_PROD.V_IE_DI_COPER.COPER_ID', $coper_id)
            ->first(['SIARU_UNICAL_PROD.VD_ANAGRAFICA.ID_AB', 'SIARU_UNICAL_PROD.VD_ANAGRAFICA.EMAIL', 'SIARU_UNICAL_PROD.VD_ANAGRAFICA.E_MAIL', 'SIARU_UNICAL_PROD.VD_ANAGRAFICA.E_MAIL_PRIVATA', 'SIAXM_UNICAL_PROD.V_IE_DI_COPER.*']);

        $ore_desc = DB::connection('oracle')->table('SIAXM_UNICAL_PROD.V_IE_DI_ORE_COPER_DET V1')
                    ->where('coper_id','=',$coper_id)
                    ->select('tipo_att_did_cod','ore','compenso_calc')
                    ->get();
        $ore_desc_string = "";
        $compenso_calcolato = 0;
        foreach ($ore_desc as $single_desc) {
            $ore_desc_string .="(";
            $ore_desc_string .=$single_desc->tipo_att_did_cod;
            $ore_desc_string .="-";
            $ore_desc_string .=$single_desc->ore;
            $ore_desc_string .=")";

            if($single_desc->compenso_calc != null)
                $compenso_calcolato += $single_desc->compenso_calc;
        }

        if($datiUgov['compenso'] == 0){
            $datiUgov['compenso'] = $compenso_calcolato;
        }
        $datiUgov['ore_desc'] = $ore_desc_string;

        $datiUgov['contatore_insegnamenti'] = InsegnamUgovController::contatoreInsegnamenti($coper_id);

        $success = true;
        return compact('datiUgov', 'message', 'success');
    }

    //condizione necessaria che il contratto corrente si CONF_INC
    public static function contatoreInsegnamenti($coper_id, $force = true) {

        //se il motivo atto è un contratto di alta qualificazione ... $this->tipoContr == 'ALTQG' || $this->tipoContr == 'ALTQC' || $this->tipoContr == 'ALTQU';
        //allora vado a cercare il APPR_INC
        $count = 0;
        //leggere da ugov insegnamento ...
        $insegnamentoUgov = InsegnamUgov::where('COPER_ID', $coper_id)
            ->first(['coper_id', 'tipo_coper_cod', 'data_ini_contratto', 'data_fine_contratto',
                'coper_peso', 'ore', 'compenso', 'motivo_atto_cod', 'tipo_atto_des', 'tipo_emitt_des',
                'numero', 'data', 'des_tipo_ciclo', 'sett_des', 'sett_cod','af_radice_id']);

        $tipo_coper_cod = $insegnamentoUgov->tipo_coper_cod;

        $datiUgov = null;

        if ( $tipo_coper_cod == 'ALTQG' ||
             $tipo_coper_cod == 'ALTQC' ||
             $tipo_coper_cod == 'ALTQU' ||
             $tipo_coper_cod == 'TC004' ||
             $tipo_coper_cod == 'TC005' ||
             $tipo_coper_cod == 'TC006'){
            //contratto di alta qualificazione
            //non c'è BAN_INC cerco il primo contratto APPR_INC
            $datiUgov = self::queryFirstMotivoAttoCod($coper_id, ['APPR_INC', 'PROP_INC']);

        }else{
            //altro
            $datiUgov = self::queryFirstMotivoAttoCod($coper_id, ['BAN_INC']);
        }

        //ATTENZIONE per i casi di rinnovi contratti già a sistema, di didattica ufficiale ma con affidamento incarico di docenza
        if (!$datiUgov){
            $datiUgov = self::queryFirstMotivoAttoCod($coper_id, ['APPR_INC', 'BAN_INC', 'PROP_INC']);
            Log::info('Conferimento incarico [ cod_coper_id: '.$coper_id.' ] tipo contratto: '.$tipo_coper_cod.' - non consistente con l\'origine dell\'attribuzione');
        }

        if ($datiUgov){
            $result = DB::connection('oracle')->table('SIAXM_UNICAL_PROD.V_IE_DI_COPER V1')->join('SIAXM_UNICAL_PROD.V_IE_DI_COPER V2', function($join) use($coper_id){
                $join->on('V2.AF_GEN_COD', '=', 'V1.AF_GEN_COD')
                     ->on(DB::raw("COALESCE(V2.SEDE_ID, 1)"), '=', DB::raw("COALESCE(V1.SEDE_ID, 1)"))
                     ->on(DB::raw("COALESCE(V2.PART_STU_ID,-1)"), '=', DB::raw("COALESCE(V1.PART_STU_ID,-1)"))
                     ->on('V2.cod_fis','=','V1.cod_fis');
            })->where('V1.COPER_ID','=',$coper_id)->where('V2.data_ini_contratto','<',$datiUgov->data_contratto_corrente)
            ->where('V2.data_ini_contratto','>=',$datiUgov->ultima_nuova_attribuzione)
            ->distinct() //elimino i contratti uguali caso PAPI e GNANI per divisione insegnamento in sezioni ...
            ->select('V1.coper_id', 'V2.motivo_atto_cod', 'V2.aa_id', 'V2.data_ini_att', 'V2.AF_GEN_COD', 'V1.sede_id', 'V2.sede_id', 'V1.PART_STU_ID', 'V2.PART_STU_ID', 'V2.data_ini_contratto', 'V2.data_fine_contratto')
            ->get();
            $count = $result->count();
        }else{
            Log::info('Conferimento incarico [ cod_coper_id: '.$coper_id.' ] senza BAN_INC o APPR_INC o PROP_INC');
            if ($force){
                //NON c'è il BAN_INC o APPR_INC conto tutti i contratti CONF_INC PRESENTI escludendo il presente
                //è un caso di errore quindi ritorno 0
                //è impostato un rinnovo ma non vengono trovati i dati per il rinnovo
                $count = DB::connection('oracle')->table('SIAXM_UNICAL_PROD.V_IE_DI_COPER V1')->join('SIAXM_UNICAL_PROD.V_IE_DI_COPER V2', function($join) use($coper_id){
                    $join->on('V2.AF_GEN_COD', '=', 'V1.AF_GEN_COD')
                        ->on(DB::raw("COALESCE(V2.SEDE_ID, 1)"), '=', DB::raw("COALESCE(V1.SEDE_ID, 1)"))
                        ->on(DB::raw("COALESCE(V2.PART_STU_ID,-1)"), '=', DB::raw("COALESCE(V1.PART_STU_ID,-1)"))
                        ->on('V2.cod_fis','=','V1.cod_fis')
                        ->on('V2.data_ini_contratto','<','V1.data_ini_contratto');
                })->where('V1.COPER_ID','=',$coper_id)->where('V2.motivo_atto_cod','=','CONF_INC')->count();
                return $count;
            }
        }

        return $count;
    }

    public static function queryFirstMotivoAttoCod($coper_id,$motivo_atto_cod_array)
    {
        $datiUgov = DB::connection('oracle')->table('SIAXM_UNICAL_PROD.V_IE_DI_COPER V1')->join('SIAXM_UNICAL_PROD.V_IE_DI_COPER V2', function($join) use($coper_id) {
            $join->on('V2.AF_GEN_COD', '=', 'V1.AF_GEN_COD')
                 ->on(DB::raw("COALESCE(V2.SEDE_ID, 1)"), '=', DB::raw("COALESCE(V1.SEDE_ID, 1)"))
                 ->on(DB::raw("COALESCE(V2.PART_STU_ID,-1)"), '=', DB::raw("COALESCE(V1.PART_STU_ID,-1)"))
                 ->on('V2.cod_fis','=','V1.cod_fis')
                 ->on('V2.data_ini_contratto','<','V1.data_ini_contratto');
        })
        ->whereIn('V2.motivo_atto_cod',$motivo_atto_cod_array)
        ->where('V1.COPER_ID','=', $coper_id)->where('V1.motivo_atto_cod','=','CONF_INC')
        ->select('V2.data_ini_contratto as ultima_nuova_attribuzione','V1.data_ini_contratto as data_contratto_corrente','V2.motivo_atto_cod as motivo_atto_cod_inizio')
        ->orderBy('V2.data_ini_contratto', 'DESC')->first();

        return $datiUgov;
    }

    public function query(Request $request){

        $app = $request->json();
        $parameters = $request->json()->all();
        $parameters['order_by'] = 'cognome,ASC|nome,ASC';
        array_push($parameters['rules'],[
            "operator" => "NotIn",
            "field" => "RUOLO_DOC_COD",
            "value" => ['PA','PO','RU']
        ]);
        array_push($parameters['rules'],[
            "operator" => "!=",
            "field" => "matricola",
            "value" => '[null]'
        ]);

        array_push($parameters['rules'],[
            "operator" => "!=",
            "field" => "DATA_INI_CONTRATTO",
            "value" => '[null]'
        ]);
        //filtro insegnamenti con rinuncia
        array_push($parameters['rules'],[
            "operator" => "NotIn",
            "field" => "STATO_COPER_COD",
            "value" => ['R']
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

            if ($uo == null) {
                abort(403, trans('global.utente_non_autorizzato'));
            }

            if ($uo->isPlesso()){
                //filtro per unitaorganizzativa dell'utente di inserimento (plesso)
                array_push($parameters['rules'],[
                    "operator" => "In",
                    "field" => "dip_cod",
                    "value" => $uo->dipartimenti()
                ]);
            } else {
                //ad un afferente al dipartimento filtro per dipartimento
                array_push($parameters['rules'],[
                    "operator" => "=",
                    "field" => "dip_cod",
                    "value" => $uo->uo
                ]);
            }

        }


        $findparam =new \App\FindParameter($parameters);

        $queryBuilder = new QueryBuilderForceInsensitive(new InsegnamUgov, $request, $findparam);

        return $queryBuilder->build()->paginate();
    }


}
