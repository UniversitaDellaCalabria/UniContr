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
    'administrator_email' =>  explode(',',env('ADMINISTRATOR_EMAIL', '')),

    /**
     * Lista email separate da , per spedizione report alle segreterie
     */
    'cc_report_segreterie' =>  explode(',',env('CC_REPORT_SEGRETERIE', '')),

    'dibest_report_segreterie'  => array_map('trim', explode(',', env('DIBEST_REPORT_SEGRETERIE', ''))),
    'ctc_report_segreterie'     => array_map('trim', explode(',', env('CTC_REPORT_SEGRETERIE', ''))),
    'dices_report_segreterie'   => array_map('trim', explode(',', env('DICES_REPORT_SEGRETERIE', ''))),
    'desf_report_segreterie'    => array_map('trim', explode(',', env('DESF_REPORT_SEGRETERIE', ''))),
    'dfssn_report_segreterie'   => array_map('trim', explode(',', env('DFSSN_REPORT_SEGRETERIE', ''))),
    'fisica_report_segreterie'  => array_map('trim', explode(',', env('FISICA_REPORT_SEGRETERIE', ''))),
    'dinci_report_segreterie'   => array_map('trim', explode(',', env('DINCI_REPORT_SEGRETERIE', ''))),
    'diam_report_segreterie'    => array_map('trim', explode(',', env('DIAM_REPORT_SEGRETERIE', ''))),
    'dimes_report_segreterie'   => array_map('trim', explode(',', env('DIMES_REPORT_SEGRETERIE', ''))),
    'dimeg_report_segreterie'   => array_map('trim', explode(',', env('DIMEG_REPORT_SEGRETERIE', ''))),
    'demacs_report_segreterie'  => array_map('trim', explode(',', env('DEMACS_REPORT_SEGRETERIE', ''))),
    'discag_report_segreterie'  => array_map('trim', explode(',', env('DISCAG_REPORT_SEGRETERIE', ''))),
    'dispes_report_segreterie'  => array_map('trim', explode(',', env('DISPES_REPORT_SEGRETERIE', ''))),
    'disu_report_segreterie'    => array_map('trim', explode(',', env('DISU_REPORT_SEGRETERIE', ''))),

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
    'firma_direttore_email' => explode(',',env('FIRMA_DIRETTORE_EMAIL',  '')),

     /**
     * Lista email separate da , per notifica compilazione terminata da parte del docente
     */
    'cmu_email' => explode(',',env('CMU_EMAIL',  '')),

    /**
     * Inserire nuovi IBAN in Ugov
     */
    'ins_iban_ugov' => env('INS_IBAN_UGOV',false),

    'db_oracle_siaru' => env('DB_UGOV_ORACLE_SIARU',''),
    'db_oracle_siaxm' => env('DB_UGOV_ORACLE_SIAXM',''),
    'db_oracle_siadg' => env('DB_UGOV_ORACLE_SIADG',''),

    'valid_email_domains' => explode(',', env('VALID_EMAIL_DOMAINS', '')),

    /** GDA upgrade */
    
    'db_oracle_gdaie' => env('DB_GDA_ORACLE_GDAIE', ''),
    
    'cineca_gda_api_base' => env('CINECA_GDA_API_BASE', 'https://bff.gda.cineca.it/api/'),
    'cineca_gda_api_programmazione' => env('CINECA_GDA_API_PROGRAMMAZIONE', 'programmazione/v1/unical/'),
    'cineca_gda_api_copertura' => env('CINECA_GDA_API_COPERTURA', 'coperture/erogata/'),
];
