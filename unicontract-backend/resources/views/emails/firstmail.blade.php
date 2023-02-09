@component('mail::message')
Gentile {{ $pre->user->nameTutorString() }},<br>
<br>
con riferimento all'incarico di insegnamento di<br>
{{ $pre->insegnamento->insegnamentoDescr }} (anno accademico {{$pre->aa}}),<br>
erogato dal {{ $pre->insegnamento->dipartimento }},<br>
presso il {{ $pre->insegnamento->dip_doc_des }}<br>
dell'Università della Calabria,<br>
La invitiamo a collegarsi il più presto possibile alla piattaforma UniContr<br>
per la compilazione online della modulistica precontrattuale necessaria<br>
alla definizione del contratto:

@component('mail::button', ['url' => $urlUniContr])
Compilazione modulistica precontrattuale
@endcomponent

Nel caso avesse in precedenza compilato la modulistica per altre docenze,<br>
troverà le informazioni da Lei già inserite in passato,<br>
che dovrà pertanto solo verificare e aggiornare ove necessario.<br>
<br>
La informiamo che per la successiva accettazione del contratto<br>
dovrà accedere al sistema con le credenziali SPID
<a href="https://www.spid.gov.it/cos-e-spid/come-attivare-spid/le-pa-per-attivare-spid/">qui come ottenere SPID</a>
<br>
<br>
Per eventuali informazioni o richieste di chiarimento,<br>
può far riferimento ai recapiti di seguito indicati.

Cordiali saluti.
@component('mail::sign')
@include( 'emails.firmamessaggioamministrazione')
@endcomponent
@endcomponent


