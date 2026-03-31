<?php

// GDA todo
// da fare

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Insegnamenti;
use App\Precontrattuale;
use App\PrecontrattualePerGenerazione;
use App\Http\Controllers\Api\V1\InsegnamentiController;
use App\Http\Controllers\Api\V1\PrecontrattualeController;
use Storage;
use App\AttachmentType;
use App\Attachment;
use App\User;
use App\Models\InsegnamGDA;
use App\Models\Anagrafica;
use PDF;
use App\Repositories\PrecontrattualeRepository;
use App\Repositories\AnagraficaRepository;
use App\Repositories\P2RapportoRepository;
use App\Repositories\B1ConflittoInteressiRepository;
use App\Repositories\A2ModalitaPagamentoRepository;
use App\Mail\FirstMail;
use Illuminate\Support\Facades\Mail;
use App\Service\PrecontrattualeService;
use App\Service\UtilService;
use App\Exports\PrecontrattualeExport;
use App\Exports\ContrGDAExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\V1\InsegnamGDAController;
use App\Http\Controllers\Api\V1\ContrGDAController;
use App;
use App\Models\GDA\ContrGDA;
use App\Models\GDA\RelazioniDgGDA;


class ContrattiTest extends TestCase
{

    // ./vendor/bin/phpunit  --testsuite Unit --filter testPrecontrattuale
    public function testPrecontrattuale()
    {
        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        $repo = new PrecontrattualeRepository($this->app);
        $response = $repo->newPrecontrImportInsegnamento(ContrattiData::getPrecontrattuale());
        $response = $repo->newIncompat(ContrattiData::getNewIncomp($response->insegn_id));
        $response = $repo->newInformativa(ContrattiData::getNewPrivacyInformativa($response->insegn_id));

        $this->assertNotNull($response->docente_id);
        $this->assertNotNull($response->insegn_id);
        $this->assertGreaterThan(0, $response->b2_incompatibilita_id);
        $this->assertGreaterThan(0, $response->b6_trattamento_dati_id);

        Precontrattuale::find($response->id)->delete();
    }

    // ./vendor/bin/phpunit  --testsuite Unit --filter testDateInsegnamento
    public function testDateInsegnamento()
    {
        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);


        foreach(Insegnamenti::where('coper_id','24550')->get() as $pre) {
            $pre->precontr()->delete();
        }
        Insegnamenti::where('coper_id','24550')->delete();



        //IMPORT INSEGNAMENTO DOCENTE
        $repo = new PrecontrattualeRepository($this->app);
        $service = new PrecontrattualeService($repo);

        $controller  = new PrecontrattualeController($repo,$service);

        $precontr = ContrattiData::getPrecontrattuale();
        $precontr['insegnamento'][ 'data_ini_contr'] = "22-12-2019";
        $precontr['insegnamento'][ 'data_fine_contr'] = "22-11-2019";

        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->replace($precontr);

        $response = $controller->newPrecontrImportInsegnamento($request);


        $this->assertNotNull($response);
        $this->assertFalse($response['success']);
    }


    /**
     * A basic test example.
     *
     * @return void
     */
    // ./vendor/bin/phpunit  --testsuite Unit --filter testInsegamentiRelation
    public function testInsegamentiRelation()
    {
        $insegn = new Insegnamenti();
        $insegn->fill(ContrattiData::getArrayContratti());
        $success = $insegn->save();
        $this->assertTrue($success);


        $precontr = $insegn->precontr()->get();
        $this->assertCount(0, $precontr);

        $insegn->delete();

    }

    // ./vendor/bin/phpunit  --testsuite Unit --filter testQueryPrecontr
    public function testQueryPrecontr(){

        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        //insegnamenti
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->replace([
            'rules' => [
                [],
            ],
            'limit' => 25,
            ]);

        $contr = new InsegnamentiController();
        $result = $contr->query($request);

        $this->assertNotNull($result);

        //precontrattuale
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $request->replace([
            'rules' => [
                [],
            ],
            'limit' => 25,
            ]);

        $repo = new PrecontrattualeRepository($this->app);
        $contr = new PrecontrattualeController($repo);
        $result = $contr->query($request);

        $this->assertNotNull($result);

    }

    //./vendor/bin/phpunit  --testsuite Unit --filter testReadStoreAttachment
    public function testReadStoreAttachment(){
        $filename = 'filetest.txt';
        Storage::disk('local')->put('filetest.txt', 'Primo contenuto');
        $contents = Storage::get('filetest.txt');

        $user = User::where('email','test.admin@unical.it')->first();

        $type = AttachmentType::where('codice','DOC_CV')->first();
        /** @var Attachment $attachment */
        $attachment = new Attachment();
        $attachment->docnumber = 'ab123';
        $attachment->emission_date = '12-03-2019';
        $attachment->model()->associate($user);
        $attachment->fromStream($contents, $filename, $type);
        $attachment->save();
        //$conv->attachments()->save($attachment);
        //echo($attachment);

        $tot = $user->attachments->count();
        $this->assertGreaterThan(0,$tot);
        $attachment->delete();
        $user->refresh();
        $tot = $tot - 1;
        $this->assertEquals($tot, $user->attachments->count());
    }

     //./vendor/bin/phpunit  --testsuite Unit --filter testGeneraPdfConflitto
    public function testGeneraPdfConflitto() {

        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        $repo = new PrecontrattualeRepository($this->app);
        $response = $repo->newPrecontrImportInsegnamento(ContrattiData::getPrecontrattuale());

        //P2
        $repo = new P2RapportoRepository($this->app);
        $repo->store(ContrattiData::getP2Rapporto($response->insegn_id));

        //ANAGRAFICA
        $repo = new AnagraficaRepository($this->app);
        $repo->store(ContrattiData::getAnagrafica($response->insegn_id));

        $repo =new B1ConflittoInteressiRepository($this->app);
        $repo->store(ContrattiData::getConflitto($response->insegn_id));

        $pre = Precontrattuale::with(['anagrafica','user','validazioni','insegnamento','conflittointeressi.cariche','conflittointeressi.incarichi'])->find($response->id);

        $pdf = PDF::loadView('pdfConflittoInteressi15',['pre' => $pre]);

        Storage::disk('local')->delete('test.pdf');

        Storage::disk('local')->put('test.pdf', $pdf->output());
        $exists = Storage::disk('local')->exists('test.pdf');

        $this->assertTrue($exists);

        Precontrattuale::find($response->id)->delete();
    }

     //./vendor/bin/phpunit  --testsuite Unit --filter testGeneraPdfConflittoTrasparenza
    public function testGeneraPdfConflittoTrasparenza() {

        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        $repo = new PrecontrattualeRepository($this->app);
        $response = $repo->newPrecontrImportInsegnamento(ContrattiData::getPrecontrattuale());

        //P2
        $repo = new P2RapportoRepository($this->app);
        $repo->store(ContrattiData::getP2Rapporto($response->insegn_id));

        //ANAGRAFICA
        $repo = new AnagraficaRepository($this->app);
        $repo->store(ContrattiData::getAnagrafica($response->insegn_id));

        $repo =new B1ConflittoInteressiRepository($this->app);
        $repo->store(ContrattiData::getConflitto($response->insegn_id));

        $pre = Precontrattuale::with(['anagrafica','user','validazioni','insegnamento','conflittointeressi.cariche','conflittointeressi.incarichi'])->find($response->id);

        $pdf = PDF::loadView('pdfConflittoInteressi15Trasparenza',['pre' => $pre]);

        Storage::disk('local')->delete('test.pdf');

        Storage::disk('local')->put('test.pdf', $pdf->output());
        $exists = Storage::disk('local')->exists('test.pdf');

        $this->assertTrue($exists);

        Precontrattuale::find($response->id)->delete();
    }

    //./vendor/bin/phpunit  --testsuite Unit --filter testSendFirstEmail
    public function testSendFirstEmail() {

        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        $repo = new PrecontrattualeRepository($this->app);
        $response = $repo->newPrecontrImportInsegnamento(ContrattiData::getPrecontrattuale());

        $this->assertNotNull($response->docente_id);
        $this->assertNotNull($response->insegn_id);

        $ctr = new InsegnamentiController();
        try {
            $result = $ctr->sendFirstEmail($response->insegn_id);

        } catch (\Throwable $th) {
            $this->assertTrue(false);
            Precontrattuale::find($response->id)->delete();
        }

        $pre = Precontrattuale::with(['sendemailsrcp'])->where('insegn_id',$response->insegn_id)->first();

        $this->assertGreaterThan(0,count($pre->sendemailsrcp));

        Precontrattuale::find($response->id)->delete();

    }



    //./vendor/bin/phpunit  --testsuite Unit --filter testGenerazioneContratto
    public function testGenerazioneContratto() {

        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        //IMPORT INSEGNAMENTO DOCENTE
        $repo = new PrecontrattualeRepository($this->app);
        $response = $repo->newPrecontrImportInsegnamento(ContrattiData::getPrecontrattuale());
        //PRESTAZIONE PROFESSIONALE
        $repo->newPrestazioneProfessionale(ContrattiData::getPrestazioneProfessionale($response->insegn_id));
        //P2
        $repo = new P2RapportoRepository($this->app);
        $repo->store(ContrattiData::getP2Rapporto($response->insegn_id));

        //ANAGRAFICA
        $repo = new AnagraficaRepository($this->app);
        $repo->store(ContrattiData::getAnagrafica($response->insegn_id));

        $result = PrecontrattualeService::createContrattoBozza($response->insegn_id);

        Storage::disk('local')->delete('test.pdf');

        Storage::disk('local')->put('test.pdf', base64_decode($result['filevalue']));
        $exists = Storage::disk('local')->exists('test.pdf');

        $this->assertTrue($exists);

        Precontrattuale::find($response->id)->delete();
    }

    //./vendor/bin/phpunit  --testsuite Unit --filter testTitulusContratto
    public function testTitulusContratto() {
        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        //IMPORT INSEGNAMENTO DOCENTE
        $repo = new PrecontrattualeRepository($this->app);
        $service = new PrecontrattualeService($repo);
        $response = $repo->newPrecontrImportInsegnamento(ContrattiData::getPrecontrattuale());

        $repo->newPrestazioneProfessionale(ContrattiData::getPrestazioneProfessionale($response->insegn_id));

        //P2
        $repo = new P2RapportoRepository($this->app);
        $repo->store(ContrattiData::getP2Rapporto($response->insegn_id));

        //ANAGRAFICA
        $repo = new AnagraficaRepository($this->app);
        $repo->store(ContrattiData::getAnagrafica($response->insegn_id));

        $result = $service->presaVisioneAccettazione($response->insegn_id);

        $this->assertNotNull($result);

        $pre = PrecontrattualePerGenerazione::with(['anagrafica','user','insegnamento','p2naturarapporto'])->where('insegn_id',$response->insegn_id)->first();
        $result = PrecontrattualeService::saveContrattoBozzaTitulus($pre);

        $this->assertNotNull($result);

        Precontrattuale::find($response->id)->delete();
    }


     // ./vendor/bin/phpunit  --testsuite Unit --filter test_exportCSV
     public function test_exportCSV(){
        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        //IMPORT INSEGNAMENTO DOCENTE
        $repo = new PrecontrattualeRepository($this->app);
        $service = new PrecontrattualeService($repo);

        $controller  = new PrecontrattualeController($repo,$service);

        //costruzione query
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $rules = json_decode('{"rules":[],"limit":1000,"sessionId":null,"page":null}',true);
        $request->json()->replace($rules);

        $findparam = new \App\FindParameter($request->all());
        //controllo numero di record restituiti
        $collection = UtilService::alldata(new Precontrattuale, $request, $findparam);
        $total = $controller->query($request)->total();
        $this->assertGreaterThanOrEqual($collection->count(), $total);

        //prendi i parametri
        $findparam = $controller->queryparameter($request);
        (new PrecontrattualeExport($request,$findparam))->store('precontrattuali.csv');

        //esportazione csv
        $response = $controller->export($request);
        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));

    }


    // ./vendor/bin/phpunit  --testsuite Unit --filter test_exportXLS
    public function test_exportXLS(){
        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        //IMPORT INSEGNAMENTO DOCENTE
        $repo = new PrecontrattualeRepository($this->app);
        $service = new PrecontrattualeService($repo);

        $controller  = new PrecontrattualeController($repo,$service);

        //costruzione query
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $rules = json_decode('{"rules":[
            {
                "field":"insegnamento.aa",
                "operator":"=",
                "value":"2019",
                "fixcondition":true,
                "type":"select"
            }
        ],"limit":1000,"sessionId":null,"page":null}',true);
        $request->json()->replace($rules);

        $findparam = new \App\FindParameter($request->json()->all());
        $findparam->includes = 'insegnamento,user,validazioni,p2naturarapporto,d1inps,d4fiscali,d2inail';
        //controllo numero di record restituiti
        $collection = UtilService::alldata(new Precontrattuale, $request, $findparam);
        $total = $controller->query($request)->total();
        $this->assertGreaterThanOrEqual($collection->count(), $total);

        //prendi i parametri
        //$findparam = $controller->queryparameter($request);
        //(new PrecontrattualeExport($request,$findparam))->store('precontrattuali.xls');

        //esportazione csv
        $response = $controller->exportxls($request);
        $this->assertEquals("attachment; filename=precontrattuali.xlsx", $response->headers->get('content-disposition'));

    }




    //./vendor/bin/phpunit  --testsuite Unit --filter testGenerazioneReport
    public function testGenerazioneReport() {

        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        $result = PrecontrattualeService::makePdfForReport('DISB');

        Storage::disk('local')->delete('test.pdf');
        Storage::disk('local')->put('test.pdf', $result->output());
        $exists = Storage::disk('local')->exists('test.pdf');

        $this->assertTrue($exists);
    }


    //./vendor/bin/phpunit  --testsuite Unit --filter test1CalcoloNumeroRinnovi
    public function test1CalcoloNumeroRinnovi() {
        //alta qualificazione
        $this->assertEquals(1, InsegnamGDAController::contatoreInsegnamenti(72204));
        //caso con contratti uguali stesso anno
        $this->assertEquals(3, InsegnamGDAController::contatoreInsegnamenti(67114));
        $this->assertEquals(3, InsegnamGDAController::contatoreInsegnamenti(66994));
    }

    //./vendor/bin/phpunit  --testsuite Unit --filter testCalcoloNumeroRinnovi
    public function testCalcoloNumeroRinnovi() {

        $datiGDA = DB::connection('oracle')
            ->table(config('unical.db_oracle_gdaie').'.ODS_L2_COPER V1')
            ->join(config('unical.db_oracle_gdaie').'.ODS_L2_COPER V2', function($join) {
            $join->on('V2.ANA_AF_COD', '=', 'V1.ANA_AF_COD')
                 ->on('V2.DOC_ID_AB','=','V1.DOC_ID_AB')
                 ->on(DB::raw("COALESCE(V2.SEDE_ID,-1)"), '=', DB::raw("COALESCE(V1.SEDE_ID,-1)"))
                 ->on(DB::raw("COALESCE(V2.PART_STU_ID,-1)"), '=', DB::raw("COALESCE(V1.PART_STU_ID,-1)"))
                 ->on('V2.DATA_INIZIO_CONTRATTO','<','V1.DATA_INIZIO_CONTRATTO')
                 ->where('V2.MOTIVO_ATTO_COD','=','BAN_INC')
                 ->where('V1.COPER_ID','=',25244);
        })
        ->select('V2.DATA_INIZIO_CONTRATTO as ultima_nuova_attribuzione','V1.DATA_INIZIO_CONTRATTO as data_contratto_corrente')
        ->orderBy('V2.DATA_INIZIO_CONTRATTO', 'DESC')->cleanGda()->first();

        $count = DB::connection('oracle')->table(config('unical.db_oracle_gdaie').'.ODS_L2_COPER V1')->join(config('unical.db_oracle_gdaie').'.ODS_L2_COPER V2', function($join) {
            $join->on('V2.ANA_AF_COD', '=', 'V1.ANA_AF_COD')
                 ->on(DB::raw("COALESCE(V2.SEDE_ID,-1)"), '=', DB::raw("COALESCE(V1.SEDE_ID,-1)"))
                 ->on(DB::raw("COALESCE(V2.PART_STU_ID,-1)"), '=', DB::raw("COALESCE(V1.PART_STU_ID,-1)"))
                 ->on('V2.DOC_ID_AB','=','V1.DOC_ID_AB')
                 ->where('V1.COPER_ID','=',25244);
        })->where('V2.DATA_INIZIO_CONTRATTO','<',$datiGDA->data_contratto_corrente)
        ->where('V2.DATA_INIZIO_CONTRATTO','>=',$datiGDA->ultima_nuova_attribuzione)->count();

        $this->assertNotNull($datiGDA);
        $this->assertEquals(2,$count);
        //caso con due sedi
        $this->assertEquals(1, InsegnamGDAController::contatoreInsegnamenti(35590));
        $this->assertEquals(2, InsegnamGDAController::contatoreInsegnamenti(32710));

        $this->assertEquals(0, InsegnamGDAController::contatoreInsegnamenti(17418));

        $this->assertEquals(2, InsegnamGDAController::contatoreInsegnamenti(25244));
        $this->assertEquals(1, InsegnamGDAController::contatoreInsegnamenti(25236));
        $this->assertEquals(0, InsegnamGDAController::contatoreInsegnamenti(23690));
        $this->assertEquals(4, InsegnamGDAController::contatoreInsegnamenti(33488));
    }


    //./vendor/bin/phpunit  --testsuite Unit --filter testStatoCivile
    public function testStatoCivile() {
        $list = Anagrafica::statoCivileLista('M');
        $this->assertEquals("Coniugato",$list[0]['value']);
        $this->assertEquals(18,count($list));
        $list = Anagrafica::statoCivileLista('F');
        $this->assertEquals("Coniugata",$list[0]['value']);
    }

  //./vendor/bin/phpunit  --testsuite Unit --filter testGenPrecontrattualeReport
  public function testGenPrecontrattualeReport() {

    $user = User::where('email','francesco.filicetti@unical.it')->first();
    $this->actingAs($user);

    //IMPORT INSEGNAMENTO DOCENTE
    $repo = new PrecontrattualeRepository($this->app);
    $response = $repo->newPrecontrImportInsegnamento(ContrattiData::getPrecontrattuale());
    //PRESTAZIONE PROFESSIONALE
    $repo->newPrestazioneProfessionale(ContrattiData::getPrestazioneProfessionale($response->insegn_id));
    //P2
    $repo = new P2RapportoRepository($this->app);
    $repo->store(ContrattiData::getP2Rapporto($response->insegn_id));

    //ANAGRAFICA
    $repo = new AnagraficaRepository($this->app);
    $repo->store(ContrattiData::getAnagrafica($response->insegn_id));

    $pres = PrecontrattualePerGenerazione::with(['anagrafica','user','insegnamento','p2naturarapporto','a2modalitapagamento','validazioni'])
        ->where('insegn_id',$response->insegn_id)->first();
    $pdf = PrecontrattualeService::makePdfPrecontrattualeReport($pres);

    Storage::disk('local')->delete('test.pdf');

    Storage::disk('local')->put('test.pdf', $pdf->output());
    $exists = Storage::disk('local')->exists('test.pdf');

    $this->assertTrue($exists);

    Precontrattuale::find($response->id)->delete();
  }

   //./vendor/bin/phpunit  --testsuite Unit --filter testGDACompensi
   public function testGDACompensi() {

        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        $contr = ContrGDA::where('ID_DG', 2045192)->first();
        $this->assertNotNull($contr);

        $rel = $contr->relazioni()->get();
        $tot_rel = $rel->count();

        $comps = $contr->compensi()->get();
        $tot_comps = $comps->count();

        //Storage::disk('local')->delete('test.pdf');
        //Storage::disk('local')->put('test.pdf', $comps[0]->stampa_conguaglio);
        //$exists = Storage::disk('local')->exists('test.pdf');

        //$this->assertTrue($exists);
        $this->assertNotNull($comps);
        $this->assertEquals(2, $tot_comps);

        foreach ($comps as $compenso) {
            $ords = $compenso->ordinativi()->get();
            $this->assertEquals(3, $ords->count());
        }

        $totRRate = $contr->relazioniRate()->get()->count();
        $this->assertEquals(2, $totRRate);

        $totRate = $contr->rate()->get()->count();
        $this->assertEquals(2, $totRate);

        $totImporto = $contr->rate()->get()->sum('importo');
        $this->assertEquals(2500, $totImporto);

        $contr = ContrGDA::with(['compensi','rate','compensi.ordinativi'])->where('id_siadi', 21354)->first(['id_x_contr','id_dg','id_siadi','num_rate','fl_gratuito','costo_totale']);
        $this->assertNotNull($contr);
        $this->assertEquals(2, $contr->rate->count());
        $this->assertEquals(2, $contr->compensi->count());
   }

    //./vendor/bin/phpunit  --testsuite Unit --filter testGDAPagamentoCompensi
    public function testGDAPagamentoCompensi() {
        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        $contrs = ContrGDA::with(['compensi','compensi.ordinativi'])
            ->has('relazioniratecompensoordinativo',DB::raw('num_rate'))
            ->whereIn('id_siadi', [21354, 22368, 25815, 24550])->get();

        $this->assertNotNull($contrs);

        foreach ($contrs as $contr) {
            $this->assertEquals($contr->num_rate, $contr->compensi->count());
        }

    }

     //vuole la connessione GDA
     // ./vendor/bin/phpunit  --testsuite Unit --filter test_ContrGDAExportCSV
     public function test_ContrGDAExportCSV(){
        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        $controller  = new ContrGDAController();

        //costruzione query
        $request = new \Illuminate\Http\Request();
        $request->setMethod('POST');
        $rules = json_decode('{"rules":[
            {
                "field":"insegnamento.aa",
                "operator":"=",
                "value":"2019",
                "fixcondition":true,
                "type":"select"
            },
            {
                "operator":"=",
                "field":"insegnamento.dip_cod",
                "type":"select",
                "value":"005019"
            }
        ],"limit":1000,"sessionId":null,"page":null}',true);
        $request->json()->replace($rules);

        //$findparam = new \App\FindParameter($request->all());

        //prendi i parametri
        $response = $controller->queryparameter($request);
        (new ContrGDAExport($request,$response["findparam"],$response["precontrs"]))->store('daticontabili.csv');

        //esportazione csv
        $response = $controller->export($request);
        $this->assertEquals('text/csv', $response->headers->get('Content-Type'));

    }

     //vuole la connessione GDA
     // ./vendor/bin/phpunit  --testsuite Unit --filter test_InseganmentiConSegmentiGDA
    public function test_InseganmentiConSegmentiGDA(){
        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        $insegnamentoGDA = InsegnamGDA::with(['segmenti'])->where(config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', 28128)

            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF',
                   config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.COPER_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS',
                   config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.MODULI_PDS_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS.MODULI_PDS_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT',
                   config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS.ANA_MOD_SETT_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.ANA_MOD_SETT_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_SETT',
                   config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.SETT_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_SETT.SETT_COD')

            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI',
                   config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.COPER_ID')

            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO',
                   config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_ATTO_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO.TIPO_ATTO_COD')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE',
               config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_EMITTENTE_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE.TIPO_EMITTENTE_COD')

            ->cleanGda()
            
            ->first([
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_COPER_COD',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_INIZIO_CONTRATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_FINE_CONTRATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.CFU',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.ORE',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COMPENSO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.MOTIVO_ATTO_COD',
                config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO.TIPO_ATTO_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE.TIPO_EMITTENTE_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.NUMERO_ATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_ATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_PERIODO_DID_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L1_SETT.SETT_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.SETT_COD',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.AF_OFF_ID'
            ]);

        $this->assertNotNull($insegnamentoGDA);
        $this->assertNotNull($insegnamentoGDA->segmenti);
        $this->assertEquals($insegnamentoGDA->segmenti->count(), 2);

        $this->assertNotNull($insegnamentoGDA->sett_desc_ita);
        $this->assertNotNull($insegnamentoGDA->sett_cod);

        $insegnamentoGDA1 = InsegnamGDA::where('COPER_ID', 28128)
            ->cleanGda()
            ->first(['coper_id', 'tipo_coper_cod', 'data_ini_contratto', 'data_fine_contratto',
                'coper_peso', 'ore', 'compenso', 'motivo_atto_cod', 'tipo_atto_des', 'tipo_emitt_des',
                'numero', 'data', 'des_tipo_ciclo', 'sett_des', 'sett_cod','af_radice_id',
                'tipo_corso_des', 'anno_corso']);

        $insegnamentoGDA = InsegnamGDA::with(['segmenti'])->where(config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', 28128)

            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF',
                   config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.COPER_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS',
                   config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.MODULI_PDS_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS.MODULI_PDS_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT',
                   config('unical.db_oracle_gdaie').'.ODS_L1_MODULI_PDS.ANA_MOD_SETT_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.ANA_MOD_SETT_ID')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_SETT',
                   config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.SETT_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_SETT.SETT_COD')

            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI',
                   config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID', '=', config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.COPER_ID')

            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO',
                   config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_ATTO_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO.TIPO_ATTO_COD')
                   
            ->join(config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE',
               config('unical.db_oracle_gdaie').'.ODS_L1_PROVVEDIMENTI.TIPO_EMITTENTE_COD', '=', config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE.TIPO_EMITTENTE_COD')

            ->cleanGda()
            
            ->first([
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COPER_ID',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_COPER_COD',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_INIZIO_CONTRATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_FINE_CONTRATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.CFU',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.ORE',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.COMPENSO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.MOTIVO_ATTO_COD',
                config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_ATTO.TIPO_ATTO_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L1_TIPI_EMITTENTE.TIPO_EMITTENTE_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.NUMERO_ATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.DATA_ATTO',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_PERIODO_DID_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L1_SETT.SETT_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L1_ANA_MOD_SETT.SETT_COD',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.AF_OFF_ID',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.TIPO_CORSO_DESC_ITA',
                config('unical.db_oracle_gdaie').'.ODS_L2_COPER.AF_OFF_ID',
                config('unical.db_oracle_gdaie').'.ODS_L1_MOD_PDS_OFF.ANNO_CORSO',
            ]);
                   
        //$this->assertNull($insegnamentoGDA1->segmenti);
        $this->assertNotNull($insegnamentoGDA1->sett_desc_ita);
        $this->assertNotNull($insegnamentoGDA1->sett_cod);

    }


    //./vendor/bin/phpunit  --testsuite Unit --filter testPrecontrattualeIbanGDAValidazione
    public function testPrecontrattualeIbanGDAValidazione() {
        $user = User::where('email','francesco.filicetti@unical.it')->first();
        $this->actingAs($user);

        //IMPORT INSEGNAMENTO DOCENTE
        $repo = new PrecontrattualeRepository($this->app);
        $service = new PrecontrattualeService($repo);
        $response = $repo->newPrecontrImportInsegnamento(ContrattiData::getPrecontrattuale());

        $repo->newPrestazioneProfessionale(ContrattiData::getPrestazioneProfessionale($response->insegn_id));

        //P2
        $repo = new P2RapportoRepository($this->app);
        $repo->store(ContrattiData::getP2Rapporto($response->insegn_id));

        //ANAGRAFICA
        $repo = new AnagraficaRepository($this->app);
        $repo->store(ContrattiData::getAnagrafica($response->insegn_id));

        $repo = new A2ModalitaPagamentoRepository($this->app);
        $datiPagamento = $repo->store(ContrattiData::getA2ModalitaPagamento($response->insegn_id));

        $msg='';
        $result = PrecontrattualeService::validazioneEconomica($response->insegn_id,
            ContrattiData::getValidazioneEconomica($response->insegn_id), $msg);

        //$result = PrecontrattualeService::saveContrattoBozzaTitulus();

        $this->assertNotNull($result);
        $this->assertTrue($result->flag_amm==1);

        Precontrattuale::find($response->id)->delete();
    }


}
