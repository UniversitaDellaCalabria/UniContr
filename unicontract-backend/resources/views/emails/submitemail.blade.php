@component('mail::message')
All'attenzione, per competenza, di:<br>
{{ $pre->insegnamento->dipartimento }}<br>
DRU – Area Professori e Ricercatori<br>
DRU - Ufficio Trattamenti Economici e Previdenziali<br>
<br>
Si informa che {{ $pre->user->nameTutorString() }} ha compilato la modulistica precontrattuale relativa al<br>
contratto di insegnamento di {{ $pre->insegnamento->insegnamentoDescr }}<br>
per l'anno accademico {{$pre->aa}}, erogato dal {{ $pre->insegnamento->dipartimento }}, presso il {{ $pre->insegnamento->dip_doc_des }}.<br>
<br>
La modulistica compilata è accessibile da [UniContr]({{$urlUniContr}})<br>
ed è in attesa di verifica e validazione da parte vostra.<br>

@component('mail::button', ['url' => $urlUniContr])
Modulistica
@endcomponent

<br>
{{ $pre->user->nameTutorString() }} <br>
{{ $pre->user->email }}
@endcomponent
