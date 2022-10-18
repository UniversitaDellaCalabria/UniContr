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
];
