@component('mail::message')
Gentile {{ $pre->user->nameTutorString() }},<br>
<br>
con riferimento all'incarico di insegnamento di<br>
{{ $pre->insegnamento->insegnamentoDescr }} (anno accademico {{$pre->aa}}),<br>
erogato dal {{ $pre->insegnamento->dipartimento }},<br>
presso il {{ $pre->insegnamento->dip_doc_des }}<br>
dell'Università della Calabria,<br>
La informiamo che la modulistica precontrattuale da Lei compilata<br>
è stata verificata e validata dagli uffici competenti, pertanto<br>
La invitiamo a prendere visione del contratto in tutte le sue parti<br>
e a confermare la sua accettazione **il più presto possibile**<br>
collegandosi di nuovo alla piattaforma UniContr<br>
utilizzando le credenziali SPID <a href="https://www.spid.gov.it/cos-e-spid/come-attivare-spid/le-pa-per-attivare-spid/">qui come ottenere SPID</a>;

@component('mail::button', ['url' => $urlUniContr])
Visione e accettazione contratto
@endcomponent

Per eventuali informazioni o richieste di chiarimento,<br>
può far riferimento ai recapiti di seguito indicati.

Cordiali saluti.
@component('mail::sign')
@include( 'emails.firmamessaggioamministrazione')
@endcomponent
@endcomponent
