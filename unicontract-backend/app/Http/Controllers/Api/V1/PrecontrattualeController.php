<?php

// GDA con riserva (cerca "GDA todo")

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Precontrattuale;
use App\PrecontrattualePerGenerazione;
use App\Models\Insegnamenti;
use Auth;
use App\Repositories\PrecontrattualeRepository;
use App\Models\Validazioni;
use App\Service\PrecontrattualeService;
use Carbon\Carbon;
use App\Service\EmailService;
use Illuminate\Support\Str;
use App\Service\TitulusHelper;
use App\Exports\PrecontrattualeExport;
use Illuminate\Support\Facades\Log;
use App\Models\InsegnamGDA;
use PHP_IBAN\IBAN;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\Handler;
use Illuminate\Container\Container;
use DB;
use Exception;

class PrecontrattualeController extends Controller
{

    /**
     * @var PrecontrattualeService
     */
    private $service;
    /**
     * @var PrecontrattualeRepository
     */
    private $repo;
    public function __construct(PrecontrattualeRepository $repo){
        $this->repo = $repo;
        $this->service = new PrecontrattualeService($repo);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $precontr = Precontrattuale::get();
        return response()->json([
            'lista' => $precontr,
            'success' => true
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [];
        $message = '';

        $precontr = new Precontrattuale();
        $postData = $request->except('id', '_method');
        $precontr->fill($postData);
        $success = $precontr->save();
        $data = $precontr;

        return compact('data', 'message', 'success');
    }

    /**
     * Update the specific resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $insegn_id)
    {
        $data = [];
        $message = '';

            $precontr = Precontrattuale::withoutGlobalScopes()->where('insegn_id', $insegn_id);
            $postData = $request->except('id', '_method');
            $success = $precontr->update($postData);
            $data = $precontr;

        return compact('data', 'message', 'success');
    }


    public function updateInsegnamentoFromGDA(Request $request){
        $data = [];
        $success = true;
        $message = '';

        //verificare stato della precontrattuale se è già validata non è aggiornabile...
        $precontr = PrecontrattualePerGenerazione::with(['validazioni','insegnamento','p2naturarapporto'])->where('insegn_id', $request->insegn_id)->first();

        if ($precontr->isBlocked()){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito');
            $success = false;
            return compact('data', 'message', 'success');
        }

        if ($precontr->validazioni->flag_amm == 1 || $precontr->validazioni->flag_upd == 1){
            //se è validata non posso aggiornare  ... prima sblocca poi si rivalida ...
            $data = [];
            $success = false;
            $message = 'Operazione di aggiornamento non eseguibile: precontrattuale validata';
            return compact('data', 'message', 'success');
        }

        //leggere da GDA insegnamento ...
        $insegnamentoGDA = InsegnamGDA::where(config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', $precontr->insegnamento->coper_id)

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

            ->first([
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_COPER_COD',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_INIZIO_CONTRATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_FINE_CONTRATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.CFU', // GDA todo // COPER_PESO non c'è su gda MA DOVREBBE ESSERE CFU
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.ORE',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COMPENSO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.MOTIVO_ATTO_COD',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_ATTO_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_EMITTENTE_DESC_ITA',
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
            

        // PATCH data rinuncia
        if($insegnamentoGDA['data_rinuncia']){
            $insegnamentoGDA['data_fine_contratto'] = explode(" ", $insegnamentoGDA['data_rinuncia'])[0];
        }

        // GDA todo
        // mancano gli atti
        $atti = DB::connection('oracle')->table(config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI A1')
                ->where('coper_id','=',$precontr->insegnamento->coper_id)
                ->where(function($query) {
                    //~ $query->where('tipo_atto_des','=','Delibera')
                          //~ ->orWhere('tipo_atto_des','=','Disposizione Direttore')
                          //~ ->orWhere('tipo_atto_des','=','Decreto Direttore');
                    $query->where('TIPO_ATTO_COD','=','DEL')
                          ->orWhere('TIPO_ATTO_COD','=','DD');
                          //~ ->orWhere('tipo_atto_des','=','Decreto Direttore');
                })
                //~ ->select('tipo_atto_des','tipo_emitt_des','motivo_atto_cod','numero','data')
                ->select('TIPO_ATTO_COD','TIPO_EMITTENTE_COD','MOTIVO_ATTO_COD','NUMERO_PROVVEDIMENTO','DATA_PROVVEDIMENTO')
                ->orderBy('DATA_PROVVEDIMENTO', 'asc')
                ->get();

        $tipo_atto_des_string = "";
        $tipo_emitt_des_string = "";
        $motivo_atto_cod_string = "";
        $numero_string = "";
        $data_string = "";

        $counter = 0;

        foreach ($atti as $atto) {
            //~ $tipo_atto_des_string .= $atto->tipo_atto_des;
            $tipo_atto_des_string .= $atto->tipo_atto_cod;
            //~ $tipo_emitt_des_string .= $atto->tipo_emitt_des;
            $tipo_emitt_des_string .= $atto->tipo_emittente_cod;
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

        $insegnamentoGDA['tipo_atto_des'] = $tipo_atto_des_string;
        $insegnamentoGDA['tipo_emitt_des'] = $tipo_emitt_des_string;
        $insegnamentoGDA['motivo_atto_cod'] = $motivo_atto_cod_string;
        $insegnamentoGDA['numero'] = $numero_string;
        $insegnamentoGDA['data'] = $data_string;
        // fine atti

        $ore_desc = DB::connection('oracle')->table(config('unical.db_oracle_gdaie').'.ODS_L1_ORE_COPER V1')
                    ->where('coper_id','=',$precontr->insegnamento->coper_id)
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

        $insegnamentoGDA['ore_desc'] = $ore_desc_string;
        if($insegnamentoGDA['compenso'] == 0){
            $insegnamentoGDA['compenso'] = $compenso_calcolato;
        }

        // PATCH per email istituzionale
        if($insegnamentoGDA['id_ab']) {
            $email = DB::connection('oracle')->table(config('unical.db_oracle_siaxm').'.V_IE_AC_PF_CONTATTI_ALL')
                    ->where('ID_AB','=',$insegnamentoGDA['id_ab'])
                    ->where('CD_TIPO_CONT','=','EMAIL')
                    ->orderBy('PRG_PRIORITA', 'desc')
                    ->get();
            if(isset($email[0]) && $email[0]->contatto){
                Log::info("Email istituzionale recuperata: ".$email[0]->contatto);
                $insegnamentoGDA['email'] = $email[0]->contatto;
            }
        }

        // PATCH - Cessazione anticipata
        if($insegnamentoGDA['data_rinuncia']) {
            $insegnamentoGDA['data_fine_contratto'] = explode(" ", $insegnamentoGDA['data_rinuncia'])[0];
        }


        //verificare la data di conferimento
        if (!$insegnamentoGDA->motivo_atto_cod){
            $message = 'Insegnamento non aggiornabile: motivo atto non inserito';
            $success = false;
            return compact('data', 'message', 'success');
        }

        //verificare motivo atto non supportato
        if ($insegnamentoGDA->motivo_atto_cod && !in_array($insegnamentoGDA->motivo_atto_cod, ['BAN_INC','APPR_INC','CONF_INC', 'PROP_INC'])){
            $message = 'Insegnamento non aggiornabile: motivo atto non supportato';
            $success = false;
            return compact('data', 'message', 'success');
        }

        //verificare la data di conferimento
        if (!$insegnamentoGDA->data_conferimento_incarico){
            $message = 'Insegnamento non aggiornabile: data conferimento non inserita';
            $success = false;
            return compact('data', 'message', 'success');
        }

        if ($insegnamentoGDA->data_inizio_contratto > $insegnamentoGDA->data_fine_contratto){
            $message = 'Insegnamento non aggiornabile: data di fine insegnamento antecedente alla data di inizio';
            $success = false;
            return compact('data', 'message', 'success');
        }

        if (($insegnamentoGDA->motivo_atto_cod=='APPR_INC' || $insegnamentoGDA->motivo_atto_cod=='PROP_INC') && !in_array($insegnamentoGDA->tipo_coper_cod, ['ALTQG',
                                                                                                        'ALTQC',
                                                                                                        'ALTQU',
                                                                                                        'TC004',
                                                                                                        'TC005',
                                                                                                        'TC006',
                                                                                                        'TC007'])){
            $data = null;
            $message = 'Insegnamento non aggiornabile: tipologia copertura non coerente con il motivo atto';
            $success = false;
            return compact('data', 'message', 'success');
        }

        if ($insegnamentoGDA->motivo_atto_cod=='BAN_INC' && !in_array($insegnamentoGDA->tipo_coper_cod, ['CONTC',
                                                                                                       'CONTU',
                                                                                                       'INTC',
                                                                                                       'INTU',
                                                                                                       'INTXU',
                                                                                                       'INTXC',
                                                                                                       'SUPPU',
                                                                                                       'SUPPC',
                                                                                                       'TC007'])){
            $data = null;
            $message = 'Insegnamento non aggiornabile: tipologia copertura non coerente con il motivo atto';
            $success = false;
            return compact('data', 'message', 'success');
        }

        if ($insegnamentoGDA->motivo_atto_cod=='CONF_INC'){
            $value = Cache::pull('counter_'.$insegnamentoGDA->coper_id);
            $contatore = InsegnamGDAController::contatoreInsegnamenti($insegnamentoGDA->coper_id, false);
            if ($contatore == 0){
                Log::info('Contatore a 0 - Importato contratto [ coper_id =' . $insegnamentoGDA->coper_id . '] [contatore insegnamenti = '.$contatore);
                $handler = new Handler(Container::getInstance());
                $handler->report(new Exception('Aggiornato contratto con contatore a 0  [ coper_id =' . $insegnamentoGDA->coper_id . ']'));
            }
        }

        $precontr->insegnamento->setDataFromGDA($insegnamentoGDA); // GDA todo

        $precontr->insegnamento->save();

        $precontr->storyprocess()->save(
            PrecontrattualeService::createStoryProcess('Modello P1: Aggiornamento dati insegnamento',
            $precontr->insegn_id)
        );

        $data = $precontr->insegnamento;

        return compact('data', 'message', 'success');
    }



    public function newPrecontrImportInsegnamento(Request $request){

        $success = true;
        $count = 0;

        //determina se un insegnamento è stato già importato e se la sua precontrattuale associata è diversa da annullato
        $precontrs = Precontrattuale::with(['insegnamento'])->whereHas('insegnamento',function($query) use($request){
            $query->where('coper_id', $request->insegnamento['coper_id']);
        })->whereNotIn('stato',[2,3])->get();

        $count = $precontrs->count();
        $data = [];

        if($count === 0) {

            $validatedData = $request->validate([
                'insegnamento.data_ini_contr' => 'required | date',
                'insegnamento.data_fine_contr' => 'required | date'
            ]);

            //verificare motivo atto
            if (!$request->insegnamento['motivo_atto']){
                $message = 'Insegnamento non importabile: motivo atto non inserito';
                $success = false;
                return compact('data', 'message', 'success');
            }

            //verificare motivo atto non supportato
            if ($request->insegnamento['motivo_atto'] && !in_array($request->insegnamento['motivo_atto'], ['BAN_INC','APPR_INC','CONF_INC','PROP_INC'])){
                $message = 'Insegnamento non importabile: motivo atto non supportato';
                $success = false;
                return compact('data', 'message', 'success');
            }

            //verificare la data di conferimento
            //if (!$request->insegnamento['data_delibera']){
            if (!explode("#", $request->insegnamento['data_delibera'])[0]){
                $message = 'Insegnamento non importabile: data conferimento non inserita';
                $success = false;
                return compact('data', 'message', 'success');
            }

            //verificare che tutte le date di conferimento
            //non siano successive alla data di inizio attività
            $date_atti = explode("#", $request->insegnamento['data_delibera']);
            $atto_precedente = false;
            foreach($date_atti as $data_atto){
                //if(!$data_atto){
                    //$message = "Insegnamento non importabile: ci sono atti di conferimento senza data";
                    //$success = false;
                    //return compact('data', 'message', 'success');
                //}
                $datetimeIni = Carbon::createFromFormat('d-m-Y', $request->insegnamento['data_ini_contr']);
                $data_atto_date = Carbon::createFromFormat('Y-m-d H:i:s', $data_atto)->format('Y-m-d');
                if($data_atto_date <= $datetimeIni){
                    $atto_precedente = true;
                    break;
                }
            }
            if(!$atto_precedente){
                $message = "Insegnamento non importabile: nessun atto di conferimento prodotto prima della data di inizio del contratto.";
                $success = false;
                return compact('data', 'message', 'success');
            }

            //verificare che tra gli atti ci sia almeno una Delibera
            $tipi_atti = explode("#", $request->insegnamento['tipo_atto']);
            $delibera_found = false;
            foreach($tipi_atti as $tipo_atto){
                if($tipo_atto == "Delibera" || $tipo_atto == "Decreto Direttore" || $tipo_atto == "Disposizione Direttore"){
                    $delibera_found = true;
                    break;
                }
            }
            if(!$delibera_found){
                $message = "Insegnamento non importabile: nessuna delibera di conferimento incarico.";
                $success = false;
                return compact('data', 'message', 'success');
            }

            //verificare chi le dati inizio fine assegnamento siano
            if ($request->insegnamento['data_ini_contr'] && $request->insegnamento['data_fine_contr']){
                $datetimeIni = Carbon::createFromFormat(config('unical.date_format'), $request->insegnamento['data_ini_contr']);
                $datetimeFine = Carbon::createFromFormat(config('unical.date_format'), $request->insegnamento['data_fine_contr']);

                if ($datetimeIni > $datetimeFine){
                    $message = 'Insegnamento non importabile: data di fine insegnamento antecedente alla data di inizio';
                    $success = false;
                    return compact('data', 'message', 'success');
                }
            }

            //verificare che al docente sia associata una email istituzionale
            if ($request->insegnamento['tipo_contratto'] && !in_array($request->insegnamento['tipo_contratto'], ['ALTQG',
                                                                                                                 'ALTQC',
                                                                                                                 'ALTQU',
                                                                                                                 'CONTC',
                                                                                                                 'CONTU',
                                                                                                                 'INTC',
                                                                                                                 'INTU',
                                                                                                                 'INTXU',
                                                                                                                 'INTXC',
                                                                                                                 'SUPPU',
                                                                                                                 'SUPPC',
                                                                                                                 'TC004',
                                                                                                                 'TC005',
                                                                                                                 'TC006',
                                                                                                                 'TC007'  ])){
                $data = null;
                $message = 'Insegnamento non importabile: tipologia di copertura non riconosciuta';
                $success = false;
                return compact('data', 'message', 'success');
            }

            //verificare che al docente sia associata una email istituzionale
            if ($request->docente['email'] && !Str::contains(strtolower($request->docente['email']), config('unical.valid_email_domains'))){
                $data = null;
                $message = 'Insegnamento non importabile: al docente '.$request->docente['name'].' non è associata una email istituzionale';
                $success = false;
                return compact('data', 'message', 'success');
            }

            if (($request->insegnamento['motivo_atto']=='APPR_INC' || $request->insegnamento['motivo_atto']=='PROP_INC') && !in_array($request->insegnamento['tipo_contratto'], ['ALTQG',
                                                                                                                          'ALTQC',
                                                                                                                          'ALTQU',
                                                                                                                          'TC004',
                                                                                                                          'TC005',
                                                                                                                          'TC006',
                                                                                                                          'TC007'])){
                $data = null;
                $message = 'Insegnamento non importabile: tipologia copertura non coerente con il motivo atto';
                $success = false;
                return compact('data', 'message', 'success');
            }

            if ($request->insegnamento['motivo_atto']=='BAN_INC' && !in_array($request->insegnamento['tipo_contratto'], ['CONTC', 'CONTU', 'INTC', 'INTU', 'INTXU', 'INTXC', 'SUPPU', 'SUPPC', 'TC007'])){
                $data = null;
                $message = 'Insegnamento non importabile: tipologia copertura non coerente con il motivo atto';
                $success = false;
                return compact('data', 'message', 'success');
            }

            if ($request->insegnamento['motivo_atto']=='CONF_INC'){
                $contatore = InsegnamGDAController::contatoreInsegnamenti($request->insegnamento['coper_id'], false);
                if ($contatore == 0){
                    Log::info('Contatore a 0 - Importato contratto [ coper_id =' . $request->insegnamento['coper_id'] . '] [contatore insegnamenti = '.$contatore);
                    $handler = new Handler(Container::getInstance());
                    $handler->report(new Exception('Importato contratto con contatore a 0  [ coper_id =' . $request->insegnamento['coper_id'] . ']'));

                    $data = null;
                    $message = 'Insegnamento non importabile come RINNOVO CONTRATTO: non ci sono precedenti insegnamenti corrispondenti';
                    $success = false;
                    return compact('data', 'message', 'success');
                }else{
                    Log::info('Importato contratto [ coper_id =' . $request->insegnamento['coper_id'] . '] [contatore insegnamenti = '.$contatore);
                }
            }

            $ore_desc = DB::connection('oracle')->table(config('unical.db_oracle_gdaie').'.ODS_L1_ORE_COPER V1')
                    ->where('coper_id','=',$request->insegnamento['coper_id'])
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

            $message = '';
            $postData = $request->except('id', '_method');
            $data = $this->repo->newPrecontrImportInsegnamento($postData, $ore_desc_string, $compenso_calcolato);
        } else {
            $success = false;
            $message = 'Insegnamento già presente nel sistema, gestire quello esistente';
        }

        return compact('data', 'message', 'success');
    }

    public function newIncompat(Request $request){

        if (!Auth::user()->hasPermissionTo('compila precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $data = [];
        $success = true;
        $message = '';
        $postData = $request->except('id', '_method');

        $data = $this->repo->newIncompat($postData);

        return compact('data', 'message', 'success');
    }

    public function newPrivacy(Request $request){

        if (!Auth::user()->hasPermissionTo('compila precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $data = [];
        $success = true;
        $message = '';
        $postData = $request->except('id', '_method');

        $data = $this->repo->newInformativa($postData);

        return compact('data', 'message', 'success');
    }

    public function newInps(Request $request){

        if (!Auth::user()->hasPermissionTo('compila precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $data = [];
        $success = true;
        $message = '';
        $postData = $request->except('id', '_method');

        $data = $this->repo->newInps($postData);

        return compact('data', 'message', 'success');
    }

    public function newPrestazioneProfessionale(Request $request){

        if (!Auth::user()->hasPermissionTo('compila precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $data = [];
        $success = true;
        $message = '';
        $postData = $request->except('id', '_method');

        $data = $this->repo->newPrestazioneProfessionale($postData);

        return compact('data', 'message', 'success');
    }

    //validazione amministrativa flagupd (prima)
    public function validazioneAmm(Request $request){

        if (!Auth::user()->hasPermissionTo('validazioneamm precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $data = [];
        $success = true;
        $message = '';

        $pre = Precontrattuale::with(['p2naturarapporto','insegnamento','anagrafica','validazioni'])->where('insegn_id', $request->insegn_id)->first();
        if ($pre->isAnnullata()){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito');
            $success = false;
            return compact('data', 'message', 'success');
        }

        $insegnamentoGDA = InsegnamGDA::where('COPER_ID', $pre->insegnamento->coper_id)->first();
        if ($insegnamentoGDA == null){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito').': il codice di copertura di questo insegnamento è stato eliminato da GDA didattica, rimuovere la precontrattuale';
            $success = false;
            return compact('data', 'message', 'success');
        }

        if (!$pre->checkCompilazioneModelli()){
            $data = [];
            $message = trans('global.validazione_non_consentita');
            $success = false;
            return compact('data', 'message', 'success');
        }

        $valid = Validazioni::where('insegn_id',$request->insegn_id)->first();

        if ($valid->flag_submit == 0){
            $data = [];
            $success = false;
            $message = 'Operazione di validazione non eseguibile';
            return compact('data', 'message', 'success');
        }

        // il dipartimento non ha caricato l'attestazione di
        // assenza di conflitto di interessi
        if ($valid->flag_confl_int_dip == 0){
            $data = [];
            $success = false;
            $message = 'Operazione di validazione non eseguibile';
            return compact('data', 'message', 'success');
        }

        $postData = $request->except('id', '_method');
        $valid->fill($postData['entity']);
        $valid->date_upd = Carbon::now()->format(config('unical.datetime_format'));

        //validata_amm
        $transitions = $valid->workflow_transitions();
        $valid->workflow_apply($transitions[0]->getName());

        $valid->save();

        $data = Validazioni::where('insegn_id',$request->insegn_id)->first();

        return compact('data', 'message', 'success');
    }

    //annullamento amministrativo flag_upd e successivi
    public function annullaAmm(Request $request){

        if (!Auth::user()->hasPermissionTo('annullaamm precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $data = [];
        $success = true;
        $message = '';

        $pre = Precontrattuale::with(['validazioni'])->where('insegn_id', $request->insegn_id)->first();
        if ($pre->isAnnullata()){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito');
            $success = false;
            return compact('data', 'message', 'success');
        }

        // se è firmata dal rettore
        if ($pre->stato == 1){
            $data = [];
            $success = false;
            $message = 'Operazione di annullamento non eseguibile';
            return compact('data', 'message', 'success');
        }

        $valid = Validazioni::where('insegn_id',$request->insegn_id)->first();

        //se è stata accetta ed è alla firma
        if ($valid->flag_accept == true){
            $data = [];
            $success = false;
            $message = 'Operazione di annullamento non eseguibile';
            return compact('data', 'message', 'success');
        }

        //flag_upd isValidatoAmm uff. amministrativo
        $transition = 'annulla_amministrativa';
        if ($valid->flag_upd && $valid->flag_amm){
            $transition = 'annulla_amministrativaeconomica';
        } else if ($valid->flag_upd && $valid->current_place == 'revisione_economica'){
            $transition = 'annulla_amministrativarevisioneeconomica';
        } else if ($valid->flag_upd && $valid->current_place == 'revisione_amministrativaeconomica_economica'){
            $transition = 'annulla_revisioneamministrativaeconomica';
        }

        //annulla_amministrativaeconomica

        if (!$valid->workflow_can($transition)){
            $data = [];
            $message = trans('global.annullamento_non_consentito');
            $success = false;
            return compact('data', 'message', 'success');
        }

        $valid->workflow_apply($transition);

        $postData = $request->except('id', '_method');
        $valid->fill($postData['entity']);
        $valid->date_upd = null;

        //annulare anche gli stati successivi
        $valid->flag_amm = false;
        $valid->date_amm = null;

        $valid->flag_accept = false;
        $valid->date_accept = null;

        $valid->save();

        $data = Validazioni::where('insegn_id',$request->insegn_id)->first();

        $entity = array_dot($postData['entity']);
        $pre->storyprocess()->save(
            PrecontrattualeService::createStoryProcess('Annullamento validazione Uff. Personale: '.$entity['note.motivazione'],
            $pre->insegn_id)
        );

        return compact('data', 'message', 'success');
    }

    //validazione economica flag_amm
    public function validazioneEconomica(Request $request){

        if (!Auth::user()->hasPermissionTo('validazioneeconomica precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $data = [];
        $success = true;
        $message = '';

        $pre = Precontrattuale::with(['p2naturarapporto','anagrafica','insegnamento','a2modalitapagamento','validazioni'])->where('insegn_id', $request->insegn_id)->first();
        if ($pre->isAnnullata()){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito');
            $success = false;
            return compact('data', 'message', 'success');
        }

        $insegnamentoGDA = InsegnamGDA::where('COPER_ID', $pre->insegnamento->coper_id)->first();
        if ($insegnamentoGDA == null){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito').': il codice di copertura di questo insegnamento è stato eliminato da GDA didattica, rimuovere la precontrattuale';
            $success = false;
            return compact('data', 'message', 'success');
        }

        if (!$pre->checkCompilazioneModelli()){
            $data = [];
            $success = false;
            $message = trans('global.validazione_non_consentita');
            return compact('data', 'message', 'success');
        }


        if ($pre->a2modalitapagamento->modality == 'ACIC' && $pre->p2naturarapporto->natura_rapporto != 'PTG'){
            $iban = new IBAN($pre->a2modalitapagamento->iban);
            if (!$iban->Verify()){
                $data = [];
                $success = false;
                $message = 'Errore: IBAN non corretto';
                return compact('data', 'message', 'success');
            }
        }

        $valid = Validazioni::where('insegn_id',$request->insegn_id)->first();

        if ($valid->flag_submit == 0 || $valid->flag_upd == 0){
            $data = [];
            $success = false;
            $message = 'Operazione di validazione non eseguibile';
            return compact('data', 'message', 'success');
        }

        $pre = Precontrattuale::with(['user'])->where('insegn_id',$request->insegn_id)->first();
        if ($pre && $pre->user->email && !Str::contains(strtolower($pre->user->email), config('unical.valid_email_domains'))){
            $data = null;
            $message = 'A '.$pre->user->nameTutorString().' non è associata una email istituzionale';
            $success = false;
            return compact('data', 'message', 'success');
        }

        $postData = $request->except('id', '_method');
        $data = PrecontrattualeService::validazioneEconomica($request->insegn_id,$postData,$message);

        return compact('data', 'message', 'success');
    }

    //annulla economica flag_amm
    public function annullaEconomica(Request $request){

        if (!Auth::user()->hasPermissionTo('annullaeconomica precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $data = [];
        $success = true;
        $message = '';

        $pre = Precontrattuale::with(['validazioni'])->where('insegn_id', $request->insegn_id)->first();
        if ($pre->isAnnullata()){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito');
            $success = false;
            return compact('data', 'message', 'success');
        }

        //se firmata dal rettore
        if ($pre->stato == 1){
            $data = [];
            $success = false;
            $message = 'Operazione di annullamento non eseguibile';
            return compact('data', 'message', 'success');
        }

        $valid = Validazioni::where('insegn_id',$request->insegn_id)->first();

        //se accettata e alla firma del rettore
        if ($valid->flag_accept == true){
            $data = [];
            $success = false;
            $message = 'Operazione di annullamento non eseguibile';
            return compact('data', 'message', 'success');
        }

        $postData = $request->except('id', '_method');
        $valid->fill($postData['entity']);
        $valid->date_amm = null;

        $valid->workflow_apply('annulla_economica');

        //annulare anche gli stati successivi
        $valid->flag_accept = false;
        $valid->date_accept = null;

        $valid->save();

        $entity = array_dot($postData['entity']);
        $pre->storyprocess()->save(
            PrecontrattualeService::createStoryProcess('Annullamento validazione Uff. Finanze: '.$entity['note.motivazione'],
            $pre->insegn_id)
        );


        $data = Validazioni::where('insegn_id',$request->insegn_id)->first();

        return compact('data', 'message', 'success');
    }

    public function presaVisioneAccettazione(Request $request){

        if (!Auth::user()->hasAllPermissions(['presavisione precontrattuale','view spid'])) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $pre = Precontrattuale::with(['validazioni'])->where('insegn_id', $request->insegn_id)->first();
        if ($pre->isAnnullata()){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito');
            $success = false;
            return compact('data', 'message', 'success');
        }

        $success = true;
        $message = '';
        $data = [];

        if (!($pre->validazioni->current_place == 'validata_economica' && !$pre->validazioni->flag_accept)){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito').' precontrattuale in validazione';
            $success = false;
            return compact('data', 'message', 'success');
        }

        $data = $this->service->presaVisioneAccettazione($request->insegn_id);

        return compact('data', 'message', 'success');
    }

    public function terminaInoltra(Request $request){
        if (!Auth::user()->hasPermissionTo('terminainoltra precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $success = true;
        $message = '';
        $data = [];

        $pre = Precontrattuale::with(['p2naturarapporto','insegnamento','anagrafica','validazioni'])->where('insegn_id', $request->insegn_id)->first();

        if ($pre->isAnnullata()){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito');
            $success = false;
            return compact('data', 'message', 'success');
        }

        if (!$pre->checkCompilazioneModelli()){
            $data = [];
            $message = trans('global.validazione_non_consentita');
            $success = false;
            return compact('data', 'message', 'success');
        }

        $insegnamentoGDA = InsegnamGDA::where('COPER_ID', $pre->insegnamento->coper_id)->first();
        if ($insegnamentoGDA == null){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito').': il codice di copertura di questo insegnamento è stato eliminato contattare la sua segreteria didattica';
            $success = false;
            return compact('data', 'message', 'success');
        }

        //aggiornamento tabella validazioni
        $valid = Validazioni::where('insegn_id', $request->insegn_id)->first();
        if (!$valid->flag_submit){
            $postData = $request->except('id', '_method');
            $data = $this->repo->terminaInoltra($postData);
        }else{
            $success = false;
            $message = 'Operazione termina già eseguita';
        }

        return compact('data', 'message', 'success');
    }

    public function annullaContratto(Request $request){
        $success = true;
        $message = '';

        if (!Auth::user()->hasPermissionTo('annullacontratto precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        //1--firmato
        //2--annullato prima firma
        //3--annullato dopo firma

        //se il contratto è firmato non si può annullare o almento ... occorre
        //allegare la delibera

        $postData = $request->except('id', '_method');
        $data = $this->repo->annullaContratto($postData, 0);

        return compact('data', 'message', 'success');
    }

    public function annullaContrattoUffici(Request $request){
        $success = true;
        $message = '';

        if (!Auth::user()->hasPermissionTo('annullacontratto precontrattuale uffici')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        //1--firmato
        //2--annullato prima firma
        //3--annullato dopo firma

        //se il contratto è firmato non si può annullare o almento ... occorre
        //allegare la delibera

        $postData = $request->except('id', '_method');
        $data = $this->repo->annullaContratto($postData, 2);

        return compact('data', 'message', 'success');
    }

    public function annullaContrattoFirmato(Request $request){
        $success = true;
        $message = '';

        if (!Auth::user()->hasPermissionTo('annullacontratto precontrattuale firmato')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        //1--firmato
        //2--annullato prima firma
        //3--annullato dopo firma

        //se il contratto è firmato non si può annullare o almento ... occorre
        //allegare la delibera

        $postData = $request->except('id', '_method');
        $data = $this->repo->annullaContratto($postData, 1);

        return compact('data', 'message', 'success');
    }

    public function rinunciaCompenso(Request $request){
        $success = true;
        $message = '';

        if (!Auth::user()->hasPermissionTo('rinuncia precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

         //se il contratto è annullato non si può modificare
        if (Precontrattuale::with(['validazioni'])->where('insegn_id', $request->insegn_id)->first()->isAnnullata()){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito');
            $success = false;
            return compact('data', 'message', 'success');
        }

        $postData = $request->except('id', '_method');
        $data = $this->repo->rinunciaCompenso($postData);

        return compact('data', 'message', 'success');
    }

    public function annullaRinuncia(Request $request){
        $success = true;
        $message = '';

        if (!Auth::user()->hasPermissionTo('rinuncia precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

         //se il contratto è annullato non si può modificare
        if (Precontrattuale::with(['validazioni'])->where('insegn_id', $request->insegn_id)->first()->isAnnullata()){
            $data = [];
            $message = trans('global.aggiornamento_non_consentito');
            $success = false;
            return compact('data', 'message', 'success');
        }

        $postData = $request->except('id', '_method');
        $data = $this->repo->annullaRinuncia($postData);

        return compact('data', 'message', 'success');
    }

    public function queryparameter(Request $request){
        $parameters = $request->json()->all();
        $parameters['includes'] = 'insegnamento,user,validazioni,p2naturarapporto';

        // se l'utente NON ha il permesso di ricerca su tutti i contratti
        if (!Auth::user()->hasPermissionTo('search all contratti')){

            //se ha il ruolo docente e il
            //e ruolo operatore dipartimentale
            //nel caso multiruolo devo scegliere un ruolo
            if (Auth::user()->hasRole('op_docente')){
                array_push($parameters['rules'],[
                    "operator" => "=",
                    "field" => "user.v_ie_ru_personale_id_ab",
                    "value" => Auth::user()->v_ie_ru_personale_id_ab
                ]);
            }else{
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
                            //"field" => "insegnamento.dip_cod",
                            "field" => "insegnamento.dip_doc_cod",
                            "value" => $uo->dipartimenti()
                        ]);
                    } else {
                        //ad un afferente al dipartimento filtro per dipartimento
                        array_push($parameters['rules'],[
                            "operator" => "=",
                            //"field" => "insegnamento.dip_cod",
                            "field" => "insegnamento.dip_doc_cod",
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
                            //"field" => "insegnamento.dip_cod",
                            "field" => "insegnamento.dip_doc_cod",
                            "value" => $sede->dipartimenti()
                        ]);
                    } else {
                        //ad un afferente al dipartimento filtro per dipartimento
                        array_push($parameters['rules'],[
                            "operator" => "=",
                            //"field" => "insegnamento.dip_cod",
                            "field" => "insegnamento.dip_doc_cod",
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
                        //"field" => "insegnamento.dip_cod",
                        "field" => "insegnamento.dip_doc_cod",
                        "value" => $lista
                    ]);
                }
            }
        }

        $findparam = new \App\FindParameter($parameters);
        return $findparam;
    }

    public function export(Request $request){
        //prendi i parametri
        $findparam = $this->queryparameter($request);
        $findparam['includes'] = 'insegnamento,user,validazioni,p2naturarapporto,d1inps,d4fiscali,d2inail,d6familiari';

        return (new PrecontrattualeExport($request,$findparam))->download('precontrattuali.csv', \Maatwebsite\Excel\Excel::CSV,  [
            'Content-Type' => 'text/csv',
            'Content-Encoding' => 'UTF-8'
        ]);
    }

    public function exportxls(Request $request){
        //prendi i parametri
        $findparam = $this->queryparameter($request);
        $findparam['includes'] = 'insegnamento,user,validazioni,p2naturarapporto,d1inps,d4fiscali,d2inail,d6familiari';

        return (new PrecontrattualeExport($request,$findparam))->download('precontrattuali.xlsx');
    }

    public function query(Request $request){
        $findparam = $this->queryparameter($request);

        $queryBuilder = new QueryBuilder(new Precontrattuale, $request, $findparam);
        $queryBuilder->alias = ['precontr.id'];

        return $queryBuilder->build()->paginate();
    }


    public function previewContratto($insegn_id){

        $result = PrecontrattualeService::previewContratto($insegn_id);

        return $result;
    }


    public function modulisticaPrecontr($insegn_id){

        $result = PrecontrattualeService::createModulisticaPrecontr($insegn_id);

        return $result;
    }


    public function getTitulusDocumentURL($id){
        return (new AttachmentController())->getTitulusDocumentURL($id);
    }


    public function downloadAttachment($id){
        //todo istanziare il controller attachment
        $attach = Attachment::find($id);
        if ($attach->num_prot){
            $app = TitulusHelper::downloadAttachment($attach->nrecord);
            if ($app){
                $attach['filevalue'] =  base64_encode($app->content);
                if ($attach->filetype == 'link'){
                    $attach['filename'] = $app->title.'.pdf';
                }
            }
        }else{
            $attach['filevalue'] =  base64_encode(Storage::get($attach->filepath));
        }
        return $attach;
    }

    public function downloadContrattoFirmato($id){
        $pre = Precontrattuale::withoutGlobalScopes()->with(['attachments','user','insegnamento'])->where('id', $id)->first();

        // se l'utente NON ha il permesso di ricerca su tutti i contratti verifico se può eseguire il download
        if (!Auth::user()->hasPermissionTo('search all contratti')){

            if (Auth::user()->hasRole('op_docente')){
                if ($pre->user->v_ie_ru_personale_id_ab != Auth::user()->v_ie_ru_personale_id_ab){
                    abort(403, trans('global.utente_non_autorizzato'));
                }
            }else{
                //aggiungere filtro per unitaorganizzativa_uo
                $uo = Auth::user()->unitaorganizzativa();
                $sede = Auth::user()->sede();

                $uo_flag = true;
                $sede_flag = true;

                if ($uo == null && $sede == null) {
                    abort(403, trans('global.utente_non_autorizzato'));
                }

                if ($uo != null) {
                    if ($uo->isPlesso()){
                        //if (!(in_array($pre->insegnamento->dip_cod,$uo->dipartimenti()))){
                        if (!(in_array($pre->insegnamento->dip_doc_cod,$uo->dipartimenti()))){
                            $uo_flag = false;
                        }

                    } else {
                        //if ($pre->insegnamento->dip_cod != $uo->uo){
                        if ($pre->insegnamento->dip_doc_cod != $uo->uo){
                            $uo_flag = false;
                        }
                    }
                }

                if ($sede != null) {
                    if ($sede->isPlesso()){
                        //if (!(in_array($pre->insegnamento->dip_cod,$uo->dipartimenti()))){
                        if (!(in_array($pre->insegnamento->dip_doc_cod,$sede->dipartimenti()))){
                            $sede_flag = false;
                        }

                    } else {
                        //if ($pre->insegnamento->dip_cod != $uo->uo){
                        if ($pre->insegnamento->dip_doc_cod != $sede->uo){
                            $sede_flag = false;
                        }
                    }
                }

                if (!$uo_flag && !$sede_flag) {
                    abort(403, trans('global.utente_non_autorizzato'));
                }
            }
        }

        if ($pre->stato == 1){
            $attach =  $pre->attachments()->where('attachmenttype_codice','CONTR_FIRMA')->first();
            if ($attach){
                return (new AttachmentController())->download($attach->id);
            }
        }
        abort(404, "Documento non trovato");
    }


    public function uploadConflIntDip(Request $request){

        if (!Auth::user()->hasPermissionTo('uploadconflintdip precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $pre = Precontrattuale::with(['validazioni'])->where('insegn_id', $request->insegn_id)->first();
        if ($pre){
            if ($pre->validazioni->flag_confl_int_dip == 0 && $pre->validazioni->flag_upd == 0){
                $postData = $request->except('id', '_method');
                if (array_key_exists('attachments',$postData)){
                    //salvare allegati ...
                    $this->repo->saveAttachments($postData['attachments'], $pre);
                }

                //aggiornare flat
                $pre->validazioni->flag_confl_int_dip = 1;
                $pre->validazioni->save();
                $data = null;
                $message = 'Operazione di upload completata con successo';
                $success = true;


                $valid = Validazioni::where('insegn_id',$request->insegn_id)->first();
                $valid->date_confl_int_dip = Carbon::now()->format(config('unical.datetime_format'));

                //validata_confl_int_dip
                //$transitions = $valid->workflow_transitions();
                //$valid->workflow_apply($transitions[0]->getName());

                $valid->save();
                $data = Validazioni::where('insegn_id',$request->insegn_id)->first();

                $pre->storyprocess()->save(
                    PrecontrattualeService::createStoryProcess('Upload dichiarazione assenza conflitto di interessi da parte del Dipartimento',
                    $pre->insegn_id)
                );
            }else{
                $data = null;
                $message = "Il documento risulta già caricato o altre validazioni impediscono l'upload";
                $success = false;
            }
        }
        return compact('data', 'message', 'success');
    }

    //annullamento amministrativo flag_upd e successivi
    public function annullaConflIntDip(Request $request){

        if (!Auth::user()->hasPermissionTo('annullaconflintdip precontrattuale')) {
            abort(403, trans('global.utente_non_autorizzato'));
        }

        $pre = Precontrattuale::with(['validazioni'])->where('insegn_id', $request->insegn_id)->first();
        if ($pre){
            if ($pre->validazioni->flag_confl_int_dip == 1 && $pre->validazioni->flag_upd == 0){
                $postData = $request->except('id', '_method');

                //aggiornare flat
                $pre->validazioni->flag_confl_int_dip = 0;
                $pre->validazioni->save();
                $data = null;
                $message = 'Operazione di annullamento completata con successo';
                $success = true;


                $valid = Validazioni::where('insegn_id',$request->insegn_id)->first();
                $valid->date_confl_int_dip = null;

                //validata_confl_int_dip
                //$transitions = $valid->workflow_transitions();
                //$valid->workflow_apply($transitions[0]->getName());

                $valid->save();
                $data = Validazioni::where('insegn_id',$request->insegn_id)->first();

                $entity = array_dot($postData['entity']);
                $pre->storyprocess()->save(
                    PrecontrattualeService::createStoryProcess('Annullamento upload dichiarazione assenza conflitto di interessi da parte del Dipartimento: '.$entity['note.motivazione'],
                    $pre->insegn_id)
                );
            }else{
                $data = null;
                $message = "Impossibile annullare l'upload";
                $success = false;
            }
        }
        return compact('data', 'message', 'success');
    }

}
