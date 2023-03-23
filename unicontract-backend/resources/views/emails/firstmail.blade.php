@component('mail::message')
Gentile {{ $pre->user->nameTutorString() }},<br>
<br>
con riferimento all'incarico di insegnamento di<br>
{{ $pre->insegnamento->insegnamentoDescr }} (anno accademico {{$pre->aa}}),<br>
erogato dal {{ $pre->insegnamento->dipartimento }},<br>
attribuito dal {{ $pre->insegnamento->dip_doc_des }}<br>
dell'Università della Calabria,<br>
La invitiamo a collegarsi il più presto possibile alla piattaforma UniContr<br>
utilizzando le credenziali di Ateneo (credenziali del sistema S.O.L.Di. - <a href="https://www.unical.it/servizi-ict/servizi-digitali-per-il-personale/credenziali-di-ateneo/">qui come ottenerle</a>)<br>
per completare la compilazione online entro 3 GIORNI della modulistica precontrattuale necessaria<br>
alla definizione del contratto:

@component('mail::button', ['url' => $urlUniContr])
Compilazione modulistica precontrattuale
@endcomponent

Per informazioni consulti la <a href="https://docs.google.com/presentation/d/1h4gPSJ25iWZsELcp6UdFzpNTLs3tgpudUjuHX03RC6Y/edit?usp=sharing">Guida alla Compilazione</a>
<br>
<br>
Nel caso avesse in precedenza compilato la modulistica per altre docenze,<br>
troverà le informazioni da Lei già inserite in passato,<br>
che dovrà pertanto solo verificare e aggiornare ove necessario.<br>
<br>
La informiamo che per la successiva accettazione del contratto<br>
dovrà accedere al sistema con le credenziali SPID
<a href="https://www.spid.gov.it/cos-e-spid/come-attivare-spid">qui come ottenere SPID</a>
<br>
<br>
Per eventuali informazioni o richieste di chiarimento,<br>
può far riferimento ai recapiti di seguito indicati.

Cordiali saluti.
@component('mail::sign')
@include( 'emails.firmamessaggioamministrazione')
@endcomponent
@endcomponent


