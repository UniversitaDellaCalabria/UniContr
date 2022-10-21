@component('mail::message')
Gentile {{ $pre->user->nameTutorString() }},

con riferimento all'incarico di insegnamento di<br>
{{ $pre->insegnamento->insegnamentoDescr }} (anno accademico {{$pre->aa}})<br>
presso il {{ $pre->insegnamento->dipartimento }}<br>
dell'Università della Calabria,<br>
<br>
il suo contratto è stato sottoscritto digitalmente dal Magnifico Rettore<br>

@component('mail::button', ['url' => $urlUniContr])
Scarica contratto firmato
@endcomponent

Cordiali saluti.<br>
@component('mail::sign')
Università della Calabria<br>
@endcomponent
@endcomponent
