<?php
use Illuminate\Database\Seeder;
use App\Convenzione;
use App\MappingRuolo;
use App\Role;
use App\Personale;
use App\Precontrattuale;
use Illuminate\Support\Facades\Hash;

//php artisan db:seed --class=DatiSeeder
//composer dump-autoload -o

////php artisan migrate:fresh --seed
class DatiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->attachmenttypes();
        $this->mappingtable();
        $this->mappingruoli();
    }



    private function onlyFirstUpper($value){
        return ucwords(mb_strtolower($value, 'UTF-8'));
    }


    private function insertOffice(Array $offices, $rolename){
        $role = Role::where('name', $rolename)->first();
        foreach ($offices as $office) {
            $mp = new MappingRuolo();
            $mp->unitaorganizzativa_uo = $office;
            $uo = $mp->unitaorganizzativa()->get()->first();
            //se esiste l'unità organizzativa
            if ($uo){
                $mp->descrizione_uo = $uo->descr;
                $mp->role_id = $role->id;
                $mp->save();
            }
        }
    }


    /**
     * Tabella di corrispondenza tra unità organizzativa e ruolo
     *
     * @return void
     */
    public function mappingruoli(){

        $this->insertOffice(config('unical.unitaSuperAdmin'), 'super-admin');
        $this->insertOffice(config('unical.unitaAdmin'), 'admin');
        $this->insertOffice(config('unical.ufficiPerValidazione'), 'op_approvazione');

        //$this->insertOffice(['005144'], 'op_approvazione_amm');
        $this->insertOffice(['000896'], 'op_approvazione_amm');
        //$this->insertOffice(['005343'], 'op_approvazione_economica');
        $this->insertOffice(['000012'], 'op_approvazione_economica');

        $this->insertOffice([// '005019',
                             // '004919',
                             // '004940',
                             // '004939',
                             // '004424',
                             // '004419',
                             // '004960',
                             // '004961',
                             // '005199',
                             '002014',
                             '002015',
                             '002021',
                             '002022',
                             '002025',
                             '002016',
                             '002018',
                             '002020',
                             '002017',
                             '002019',
                             '002013',
                             '002024',
                             '002026',
                             '002023',
                            ],
                            'op_dipartimentale');

    }


    /**
     * Tabella di corrispondenza unità organizzativa e struttura interna
     *
     * @return void
     */
    public function mappingtable(){

        // DB::table('mappinguffici')->insert([
        //     'unitaorganizzativa_uo' => '005199',
        //     'descrizione_uo' => 'Plesso Scientifico (DiSPeA-DiSB)',
        //     'strutturainterna_cod_uff' => 'SI000083',
        //     'descrizione_uff' => 'Plesso Scientifico (DISPeA e DISB)',
        // ]);

        // DB::table('mappinguffici')->insert([
        //     'unitaorganizzativa_uo' => '005361',
        //     'descrizione_uo' => 'Ufficio Ricerca e Relazioni Internazionali - Sett. Ric. e Terza miss.',
        //     'strutturainterna_cod_uff' => 'SI000048',
        //     'descrizione_uff' => 'Ufficio Ricerca e Relazioni Internazionali',
        // ]);

        // if (App::environment('local') || App::environment('preprod')) {
        //     DB::table('mappinguffici')->insert([
        //         'unitaorganizzativa_uo' => '005400',
        //         'descrizione_uo' => 'Attività Sistemistiche  e Software Gestionali e Documentali - S.S.I.A.',
        //         'strutturainterna_cod_uff' => 'Uf1_51',
        //         'descrizione_uff' => 'Ufficio Protocollo e Archivio',
        //     ]);
        // }else{
        //     DB::table('mappinguffici')->insert([
        //         'unitaorganizzativa_uo' => '005400',
        //         'descrizione_uo' => 'Attività Sistemistiche  e Software Gestionali e Documentali - S.S.I.A.',
        //         'strutturainterna_cod_uff' => 'SI000099',
        //         'descrizione_uff' => 'Attività sistemistiche e software Gestionali e Documentali',
        //     ]);
        // }

        // DB::table('mappinguffici')->insert([
        //     'unitaorganizzativa_uo' => '005479',
        //     'descrizione_uo' => 'Rete Dati e Voce, Servizi Telematici e Assistenza Informatica - S.S.I.A.',
        //     'strutturainterna_cod_uff' => 'SI000100',
        //     'descrizione_uff' => 'Rete Dati e Voce, Servizi Telematici e Assistenza Informatica',
        // ]);

        //004939	Dipartimento di Studi Umanistici (DISTUM) Dipartimento di Studi Umanistici (DISTUM) SI000089 PI000073
        // DB::table('mappinguffici')->insert([
        //     'unitaorganizzativa_uo' => '004939',
        //     'descrizione_uo' => 'Dipartimento di Studi Umanistici (DISTUM)',
        //     'strutturainterna_cod_uff' => 'SI000089',
        //     'descrizione_uff' => 'Dipartimento di Studi Umanistici (DISTUM)',
        // ]);

        //004424	Dipartimento di Economia, Società, Politica (DESP) - Dipartimento di Economia, Società, Politica (DESP) SI000058 PI000073
        // DB::table('mappinguffici')->insert([
        //     'unitaorganizzativa_uo' => '004424',
        //     'descrizione_uo' => 'Dipartimento di Economia, Società, Politica (DESP)',
        //     'strutturainterna_cod_uff' => 'SI000058',
        //     'descrizione_uff' => 'Dipartimento di Economia, Società, Politica (DESP)',
        // ]);

        //004419	Dipartimento di Giurisprudenza - Dipartimento di Giurisprudenza SI000062 PI000056
        // DB::table('mappinguffici')->insert([
        //     'unitaorganizzativa_uo' => '004419',
        //     'descrizione_uo' => 'Dipartimento di Giurisprudenza',
        //     'strutturainterna_cod_uff' => 'SI000062',
        //     'descrizione_uff' => 'Dipartimento di Giurisprudenza',
        // ]);

        //004940	Dipartimento di Scienze della Comunicazione, Studi Umanistici e Internazionali: Storia, Culture, Lingue, Letterature, Arti, Media (DISCUI) - Dipartimento di Scienze della Comunicazione, Studi Umanistici e Internazionali: Storia, Culture, Lingue, Letterature, Arti, Media - DISCUI SI000087 PI000056
        // DB::table('mappinguffici')->insert([
        //     'unitaorganizzativa_uo' => '004940',
        //     'descrizione_uo' => 'Dipartimento di Scienze della Comunicazione, Studi Umanistici e Internazionali: Storia, Culture, Lingue, Letterature, Arti, Media (DISCUI)',
        //     'strutturainterna_cod_uff' => 'SI000087',
        //     'descrizione_uff' => 'Dipartimento di Scienze della Comunicazione, Studi Umanistici e Internazionali: Storia, Culture, Lingue, Letterature, Arti, Media - DISCUI',
        // ]);


        //005579	Dipartimento di Scienze della Comunicazione, Studi Umanistici e Internazionali (DISCUI) SI000087 PI000056
        // DB::table('mappinguffici')->insert([
        //     'unitaorganizzativa_uo' => '005579',
        //     'descrizione_uo' => 'Dipartimento di Scienze della Comunicazione, Studi Umanistici e Internazionali (DISCUI)',
        //     'strutturainterna_cod_uff' => 'SI000087',
        //     'descrizione_uff' => 'Dipartimento di Scienze della Comunicazione, Studi Umanistici e Internazionali: Storia, Culture, Lingue, Letterature, Arti, Media - DISCUI',
        // ]);


        //005019	Dipartimento di Scienze Biomolecolari (DISB) - Dipartimento di Scienze Biomolecolari SI000060 PI000083
        // DB::table('mappinguffici')->insert([
        //     'unitaorganizzativa_uo' => '005019',
        //     'descrizione_uo' => 'Dipartimento di Scienze Biomolecolari (DISB)',
        //     'strutturainterna_cod_uff' => 'SI000060',
        //     'descrizione_uff' => 'Dipartimento di Scienze Biomolecolari',
        // ]);


        //004919	Dipartimento di Scienze Pure e Applicate (DiSPeA) - Dipartimento di Scienze Pure e Applicate - DISPeA SI000084 PI000083
        // DB::table('mappinguffici')->insert([
        //     'unitaorganizzativa_uo' => '004919',
        //     'descrizione_uo' => 'Dipartimento di Scienze Pure e Applicate (DiSPeA)',
        //     'strutturainterna_cod_uff' => 'SI000084',
        //     'descrizione_uff' => 'Dipartimento di Scienze Pure e Applicate - DISPeA',
        // ]);

        // UNICAL

        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002032',
            'descrizione_uo' => 'Area Servizi Informatici e Tecnologici - A.S.I.T.',
            'strutturainterna_cod_uff' => '2032.2',
            'descrizione_uff' => 'AREA SISTEMI INFORMATIVI',
        ]);

        // 002014	Dipartimento di Biologia, Ecologia e Scienze della Terra (DiBEST)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002014',
            'descrizione_uo' => 'Dipartimento di Biologia, Ecologia e Scienze della Terra (DiBEST)',
            'strutturainterna_cod_uff' => '2014',
            'descrizione_uff' => 'DIPARTIMENTO DI BIOLOGIA, ECOLOGIA E SCIENZE DELLA TERRA',
        ]);

        // 002015	Dipartimento di Chimica e Tecnologie Chimiche (CTC)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002015',
            'descrizione_uo' => 'Dipartimento di Chimica e Tecnologie Chimiche (CTC)',
            'strutturainterna_cod_uff' => '2015',
            'descrizione_uff' => 'DIPARTIMENTO DI CHIMICA E TECNOLOGIE CHIMICHE',
        ]);

        // 002022	Dipartimento di Culture, Educazione e Società (DiCES)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002022',
            'descrizione_uo' => 'Dipartimento di Culture, Educazione e Società (DiCES)',
            'strutturainterna_cod_uff' => '2022',
            'descrizione_uff' => "DIPARTIMENTO DI CULTURE EDUCAZIONE SOCIETA'",
        ]);

        // 002025	Dipartimento di Economia, Statistica e Finanza (DESF)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002025',
            'descrizione_uo' => 'Dipartimento di Economia, Statistica e Finanza (DESF)',
            'strutturainterna_cod_uff' => '2025',
            'descrizione_uff' => 'DIPARTIMENTO DI ECONOMIA STATISTICA E FINANZA',
        ]);

        // 002021	Dipartimento di Farmacia e Scienze della Salute e della Nutrizione (DFSSN)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002021',
            'descrizione_uo' => 'Dipartimento di Farmacia e Scienze della Salute e della Nutrizione (DFSSN)',
            'strutturainterna_cod_uff' => '2021',
            'descrizione_uff' => 'DIPARTIMENTO DI FARMACIA',
        ]);

        // 002016	Dipartimento di Fisica
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002016',
            'descrizione_uo' => 'Dipartimento di Fisica',
            'strutturainterna_cod_uff' => '2016',
            'descrizione_uff' => 'DIPARTIMENTO DI FISICA',
        ]);

        // 002018	Dipartimento di Ingegneria Civile (DInCi)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002018',
            'descrizione_uo' => 'Dipartimento di Ingegneria Civile (DInCi)',
            'strutturainterna_cod_uff' => '2018',
            'descrizione_uff' => 'DIPARTIMENTO DI INGEGNERIA CIVILE',
        ]);

        // 002020	Dipartimento di Ingegneria dell'Ambiente (DIAm)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002020',
            'descrizione_uo' => "Dipartimento di Ingegneria dell'Ambiente (DIAm)",
            'strutturainterna_cod_uff' => '2020',
            'descrizione_uff' => "DIPARTIMENTO DI INGEGNERIA DELL'AMBIENTE",
        ]);

        // 002017	Dipartimento di Ingegneria Informatica, Modellistica, Elettronica e Sistemistica (DIMES)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002017',
            'descrizione_uo' => "Dipartimento di Ingegneria Informatica, Modellistica, Elettronica e Sistemistica (DIMES)",
            'strutturainterna_cod_uff' => '2017',
            'descrizione_uff' => "DIPARTIMENTO DI INGEGNERIA INFORMATICA MODELLISTICA ELETTRONICA E SISTEMISTICA",
        ]);

        // 002019	Dipartimento di Ingegneria Meccanica, Energetica e Gestionale (DIAm)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002019',
            'descrizione_uo' => "Dipartimento di Ingegneria Meccanica, Energetica e Gestionale (DIMEG)",
            'strutturainterna_cod_uff' => '2019',
            'descrizione_uff' => "DIPARTIMENTO DI INGEGNERIA MECCANICA ENERGETICA E GESTIONALE",
        ]);

        // 002013	Dipartimento di Matematica e Informatica (DEMACS)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002013',
            'descrizione_uo' => "Dipartimento di Matematica e Informatica (DEMACS)",
            'strutturainterna_cod_uff' => '2013',
            'descrizione_uff' => "DIPARTIMENTO DI MATEMATICA E INFORMATICA",
        ]);

        // 002024	Dipartimento di Scienze Aziendali e Giuridiche (DiScAG)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002024',
            'descrizione_uo' => "Dipartimento di Scienze Aziendali e Giuridiche (DiScAG)",
            'strutturainterna_cod_uff' => '2024',
            'descrizione_uff' => "DIPARTIMENTO DI SCIENZE AZIENDALI E GIURIDICHE",
        ]);

        // 002026	Dipartimento di Scienze Politiche e Sociali (DiSPeS)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002026',
            'descrizione_uo' => "Dipartimento di Scienze Politiche e Sociali (DiSPeS)",
            'strutturainterna_cod_uff' => '2026',
            'descrizione_uff' => "DIPARTIMENTO DI SCIENZE POLITICHE E SOCIALI",
        ]);

        // 002023	Dipartimento di Studi Umanistici (DiSU)
        DB::table('mappinguffici')->insert([
            'unitaorganizzativa_uo' => '002023',
            'descrizione_uo' => "Dipartimento di Studi Umanistici (DiSU)",
            'strutturainterna_cod_uff' => '2023',
            'descrizione_uff' => "DIPARTIMENTO DI STUDI UMANISTICI",
        ]);

    }

    /**
     * Tipi di allegato
     *
     * @return void
     */
    public function attachmenttypes(){
        DB::table('attachmenttypes')->insert([
            'codice' => 'DOC_CV',
            'gruppo' => 'anagrafica',
            'descrizione' => 'Curriculum',
            'descrizione_compl' => 'Curriculum',
            'parent_type' => User::class,
        ]);

        DB::table('attachmenttypes')->insert([
            'codice' => 'DOC_CI',
            'gruppo' => 'anagrafica',
            'descrizione' => 'Carta di identità',
            'descrizione_compl' => 'Carta di identità',
            'parent_type' => User::class,
        ]);

        DB::table('attachmenttypes')->insert([
            'codice' => 'AUT_PA',
            'gruppo' => 'B4RapportoPA',
            'descrizione' => 'Autorizzazione PA',
            'descrizione_compl' => 'Autorizzazione Pubblica Amministrazione',
            'parent_type' => B4RapportoPA::class,
        ]);

        DB::table('attachmenttypes')->insert([
            'codice' => 'CONTR_BOZZA',
            'gruppo' => 'Precontrattuale',
            'descrizione' => 'Contratto bozza',
            'descrizione_compl' => 'Contratto in stato di bozza',
            'parent_type' => Precontrattuale::class,
        ]);
        DB::table('attachmenttypes')->insert([
            'codice' => 'CONTR_FIRMA',
            'gruppo' => 'Precontrattuale',
            'descrizione' => 'Contratto',
            'descrizione_compl' => 'Contratto',
            'parent_type' => Precontrattuale::class,
        ]);

        DB::table('attachmenttypes')->insert([
            'codice' => 'DOM_GS',
            'gruppo' => 'D1_Inps',
            'descrizione' => 'Iscrizione GS',
            'descrizione_compl' => 'Domanda di iscrizione alla Gestione Separata',
            'parent_type' => D1_Inps::class,
        ]);

        DB::table('attachmenttypes')->insert([
            'codice' => 'CONFL_INT_15',
            'gruppo' => 'Precontrattuale',
            'descrizione' => 'Art. 15',
            'descrizione_compl' => 'Dichiarazione conflitto interessi',
            'parent_type' => Precontrattuale::class,
        ]);

        DB::table('attachmenttypes')->insert([
            'codice' => 'CONFL_INT',
            'gruppo' => 'Precontrattuale',
            'descrizione' => 'Dichiarazione coflitto interessi',
            'descrizione_compl' => 'Dichiarazione conflitto interessi',
            'parent_type' => Precontrattuale::class,
        ]);

    }
}
