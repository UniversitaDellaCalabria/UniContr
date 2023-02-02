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
    'cc_report_segreterie' =>  explode(',',env('CC_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it')),

    'dibest_report_segreterie' =>  array_map('trim',explode(',',env('DIBEST_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'ctc_report_segreterie' =>  array_map('trim',explode(',',env('CTC_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'dices_report_segreterie' =>  array_map('trim',explode(',',env('DICES_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'desf_report_segreterie' =>  array_map('trim',explode(',',env('DESF_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'dfssm_report_segreterie' =>  array_map('trim',explode(',',env('DFSSN_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'fisica_report_segreterie' =>  array_map('trim',explode(',',env('FISICA_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'dinci_report_segreterie' =>  array_map('trim',explode(',',env('DINCI_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'diam_report_segreterie' =>  array_map('trim',explode(',',env('DIAM_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'dimes_report_segreterie' =>  array_map('trim',explode(',',env('DIMES_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'dimeg_report_segreterie' =>  array_map('trim',explode(',',env('DIMEG_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'demacs_report_segreterie' =>  array_map('trim',explode(',',env('DEMACS_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'discag_report_segreterie' =>  array_map('trim',explode(',',env('DISCAG_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'dispes_report_segreterie' =>  array_map('trim',explode(',',env('DISPES_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),
    'disu_report_segreterie' =>  array_map('trim',explode(',',env('DISU_REPORT_SEGRETERIE', 'francesco.filicetti@unical.it'))),

    /**
     * Lista email separate da , per notifica di visione accettazione da parte del docente
     */
    'firma_direttore_email' => explode(',',env('FIRMA_DIRETTORE_EMAIL',  'firma@unical.it')),

     /**
     * Lista email separate da , per notifica compilazione terminata da parte del docente
     */
    'cmu_email' => explode(',',env('CMU_EMAIL',  'unicontract@unical.it,amministrazione.reclutamento.pdoc@unical.it')),

    /**
     * Inserire nuovi IBAN in Ugov
     */
    'ins_iban_ugov' => env('INS_IBAN_UGOV',false),

    'db_oracle_siaru' => env('DB_ORACLE_SIARU',''),
    'db_oracle_siaxm' => env('DB_ORACLE_SIAXM',''),
    'db_oracle_siadg' => env('DB_ORACLE_SIADG',''),
];
