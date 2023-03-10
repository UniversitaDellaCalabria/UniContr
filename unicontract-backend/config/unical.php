<?php
/**
 * uniurb/unidem package configuration file.
 */
return [

    /**
     * Configurazione data e ora:
     */
    'date_format'        => 'd-m-Y',
    'time_format'        => 'H:i:s',
    'datetime_format' => 'd-m-Y H:i:s',
    'timezone' => 'Europe/Rome',

    'date_format_contratto' => 'd/m/Y',


    'route'              => '',
    /**
     * URL target per l'applicazione client
     */
    'client_url' => env('CLIENT_URL', 'https://unicontr.unical.it/'),

    //Configurazioni per dati di seed

    /**
     * Codici unità organizzativa associati al ruolo super-admin
     * esempio: '002032'
     */
    'unitaSuperAdmin' => [''],
    /**
     * Codici unità organizzativa associati al ruolo admin
     */
    'unitaAdmin' => explode(',',env('UFF_ADMIN','')),
    /**
     * Codici unità organizzativa associati al ruolo op_approvazione
     * esempio '000896,000012'
     */
    'ufficiPerValidazione' =>  explode(',',env('UFF_VALIDAZIONE', '')),

    //configurazione email

    /**
     * Lista email separate da , per amministratori di sistema
     */
    'administrator_email' =>  explode(',',env('ADMINISTRATOR_EMAIL', 'francesco.filicetti@unical.it')),

    /**
     * Lista email separate da , per spedizione report alle segreterie
     */
    'cc_report_segreterie' =>  explode(',',env('CC_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it,giuliana.gabrieli@unical.it,giuseppe.rossi@unical.it')),

    'dibest_report_segreterie' =>  array_map('trim',explode(',',env('DIBEST_REPORT_SEGRETERIE', 'r.zicarelli@unical.it,emanuele.dodaro@unical.it'))),
    'ctc_report_segreterie' =>  array_map('trim',explode(',',env('CTC_REPORT_SEGRETERIE', 'giovanna.bonadies@unical.it'))),
    'dices_report_segreterie' =>  array_map('trim',explode(',',env('DICES_REPORT_SEGRETERIE', 'walter.borrelli@unical.it,antonio.cannataro@unical.it,perricone@unical.it,anna.morrone@unical.it'))),
    'desf_report_segreterie' =>  array_map('trim',explode(',',env('DESF_REPORT_SEGRETERIE', 'roberto.antonucci@unical.it,monica.veneziani@unical.it'))),
    'dfssn_report_segreterie' =>  array_map('trim',explode(',',env('DFSSN_REPORT_SEGRETERIE', 'p.cicirelli@unical.it,v.filippelli@unical.it,francesco.portadibasso@unical.it,cinzia.volpone@unical.it'))),
    'fisica_report_segreterie' =>  array_map('trim',explode(',',env('FISICA_REPORT_SEGRETERIE', 'fabiana.fuscaldo@unical.it,chiara.chiodi@unical.it'))),
    'dinci_report_segreterie' =>  array_map('trim',explode(',',env('DINCI_REPORT_SEGRETERIE', 'm.gencarelli@unical.it,pierfrancesco.santoro@unical.it,annamaria.trecroci@unical.it'))),
    'diam_report_segreterie' =>  array_map('trim',explode(',',env('DIAM_REPORT_SEGRETERIE', 'michela.rombola@unical.it,gioia.deraffele@unical.it'))),
    'dimes_report_segreterie' =>  array_map('trim',explode(',',env('DIMES_REPORT_SEGRETERIE', 'ilaria.gallo@unical.it,lucia.pullano@dimes.unical.it,a.soloperto@unical.it'))),
    'dimeg_report_segreterie' =>  array_map('trim',explode(',',env('DIMEG_REPORT_SEGRETERIE', 'assunta.greco@unical.it,samantha.salmena@unical.it,maria_rosa.taccone@unical.it'))),
    'demacs_report_segreterie' =>  array_map('trim',explode(',',env('DEMACS_REPORT_SEGRETERIE', 'gianluca.tricoli@unical.it,irene.santoro@unical.it'))),
    'discag_report_segreterie' =>  array_map('trim',explode(',',env('DISCAG_REPORT_SEGRETERIE', 'maria.assalone@unical.it,francesco.greco70@unical.it,rosetta.miracco@unical.it,ruggero.vetere@unical.it'))),
    'dispes_report_segreterie' =>  array_map('trim',explode(',',env('DISPES_REPORT_SEGRETERIE', 'pierluigi.fucilla@unical.it,santina.orlando@unical.it,pietrina.zolo@unical.it'))),
    'disu_report_segreterie' =>  array_map('trim',explode(',',env('DISU_REPORT_SEGRETERIE', 'orfeo.massara@unical.it,luigi.attento@unical.it,alessandro.sole@unical.it'))),

    // Department codes
    '002014' => 'DiBEST',
    '002015' => 'CTC',
    '002022' => 'DiCES',
    '002025' => 'DESF',
    '002021' => 'DFSSN',
    '002016' => 'Fisica',
    '002018' => 'DInCi',
    '002020' => 'DIAm',
    '002017' => 'DIMES',
    '002019' => 'DIMEG',
    '002013' => 'DEMACS',
    '002024' => 'DiScAG',
    '002026' => 'DiSPeS',
    '002023' => 'DiSU',

    /**
     * Lista email separate da , per notifica di visione accettazione da parte del docente
     */
    'firma_direttore_email' => explode(',',env('FIRMA_DIRETTORE_EMAIL',  'roberto.elmo@unical.it,giuseppe.rossi@unical.it')),

     /**
     * Lista email separate da , per notifica compilazione terminata da parte del docente
     */
    'cmu_email' => explode(',',env('CMU_EMAIL',  'daniela.pisani@unical.it,paola.cordiale@unical.it,silvia.pagano@unical.it,egidio.cario@unical.it')),

    /**
     * Inserire nuovi IBAN in Ugov
     */
    'ins_iban_ugov' => env('INS_IBAN_UGOV',false),

    'db_oracle_siaru' => env('DB_ORACLE_SIARU',''),
    'db_oracle_siaxm' => env('DB_ORACLE_SIAXM',''),
    'db_oracle_siadg' => env('DB_ORACLE_SIADG',''),

    'valid_email_domains' => explode(',', env('VALID_EMAIL_DOMAINS', '@unical.it,@mat.unical.it,@dimes.unical.it,@deis.unical.it,@fis.unical.it'))
];
