@component('mail::message')
Gentile {{ $pre->user->nameTutorString() }},

con riferimento all'incarico di insegnamento di<br>
{{ $pre->insegnamento->insegnamentoDescr }} (anno accademico {{$pre->aa}}),<br>
erogato dal {{ $pre->insegnamento->dipartimento }},<br>
attribuito dal {{ $pre->insegnamento->dip_doc_des }}<br>
dell'Università della Calabria,<br>
<br>
Le inviamo, in allegato, il contratto sottoscritto digitalmente dal Magnifico Rettore.<br>

@if($titulus)
<br>
<br>
Numero di protocollo: {{ $titulus->num_protocollo }}<br>
Data protocollo: {{ $titulus->data_protocollo }}
<br>
<br>
@endif

Cordiali saluti.<br>
@component('mail::sign')
Università della Calabria<br>
@endcomponent
@endcomponent
