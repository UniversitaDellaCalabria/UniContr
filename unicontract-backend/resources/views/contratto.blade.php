<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<style type="text/css">
	  html, body, p, ul, li, span, img {
      margin: 0px;
      padding: 0px;
    }
    body {
      margin-left: 30mm;
      font-family:  Arial, Helvetica, sans-serif;
      font-size: 13pt;
      text-rendering: geometricPrecision;

    }

    .bozza {
      background: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' version='1.1' height='1200px' width='1000px'><text x='700' y='300' fill='#f7d0cd' font-size='150pt' font-family='Arial' transform='rotate(45)'>BOZZA</text></svg>");
      background-repeat: repeat-y;
      background-position: left center;
      background-attachment: fixed;
      background-size:100%;
    }

	  h4 {
      font-size: 13pt;
      text-align: center;
      line-height: 1.7;
      text-rendering: geometricPrecision;
	  }
    div.page
    {
        page-break-after: always;
        page-break-inside: avoid;
    }
	  .normal {
		font-family:  Arial, Helvetica, sans-serif;
		font-size: 13pt;
    text-rendering: geometricPrecision;
 		line-height: 1.7;
		text-align: justify
	  }
    .small {
      font-family:  Arial, Helvetica, sans-serif;
      font-size: 11pt;
      text-rendering: geometricPrecision;
      line-height: 1.7;
      text-align: left
	  }
    .infor {
      font-family:  Arial, Helvetica, sans-serif;
      font-size: 11pt;
      text-rendering: geometricPrecision;
      line-height: 1.3;
      text-align: justify
	  }
    .subtitleinfor {
      font-family:  Arial, Helvetica, sans-serif;
      font-size: 12pt;
      font-weight: bold;
      text-rendering: geometricPrecision;
      line-height: 1.5;
      text-align: left
    }
	  .piepagina {
      font-size: 9pt;
      font-family: Arial, Helvetica, sans-serif;
      text-align: left;
	  }

    .logo{
           background-image: url("https://{{ Request::getHost() }}/public/img/logo_unical.png");
           background-size: contain;
           background-repeat: no-repeat;
           background-position: center;
           width: 100%;
           height: 200px;
    }

	</style>
</head>
<body class="@if ($type=='CONTR_BOZZA') bozza @endif">


	<div class="page">

    <div class="logo" >     </div>

	<h4>UNIVERSITA' DELLA CALABRIA</h4>


	@include( 'contratto.premessa.premessaCont', $pre)
    @include( 'contratto.articoli.art1-2-3-4-5', $pre)
    @include( 'contratto.articoli.art6-7-8-9', $pre)
    @include( 'contratto.articoli.art10-11-12-13', $pre)

    <br>
    <p class="small">
    RENDE (CS), (data ultima sottoscrizione digitale in ordine cronologico attestata dalla marcatura temporale)
    </p>
    <br>
    <p class="normal">
    Il Rettore Nicola Leone
    </p>
    <br>
    <p class="normal">
    {{$pre->genere['str0']}} {{$pre->genere['str5']}} {{ $pre->user->nameTutorString() }}
    </p>
    <br>
    <br>
	<br>
	<br>
	<p class="small">
    <i>{{$pre->genere['str0']}} {{$pre->genere['str5']}} dichiara di ricevere copia del Codice di Comportamento dell’Università della Calabria e di averlo sottoscritto in data odierna. Dichiara, altresì, di ricevere copia del “Piano Integrato delle Attività e Organizzazione (PIAO), contenente il Sistema di prevenzione della Corruzione e attuazione della Trasparenza, e copia dell’Informativa rivolta ai collaboratori esterni (con contratto di lavoro autonomo) nel rispetto della normativa vigente in materia di protezione dei dati personali (Regolamento UE 2016/679, RGPD).	</i>
	</p>
    <br>
	<p class="normal">
    {{$pre->genere['str0']}} {{$pre->genere['str5']}} {{ $pre->user->nameTutorString() }}
	</p>
    </div>
    {{-- INFORMATIVA SULLA PRIVACY --}}
    <div class="page ">
		<div class="logo" > </div>
		@include( 'contratto.codice_comportamento', $pre)
	</div>

	<div class="page ">
		<div class="logo" >	</div>
		@include( 'contratto.informativa', $pre)
	</div>

	<div class="page ">
		<div class="logo" > </div>
		@include( 'contratto.piao', $pre)

        @if ($show_sign)
            <br>
            <br>
            <div class="piepagina">
                Sottoscritto da {{ $pre->user->nameTutorString() }}
                su https://unicontr.unical.it
                il {{ Carbon::now()->setTimezone(config('unical.timezone'))->format('d/m/Y') }}
                alle {{ Carbon::now()->setTimezone(config('unical.timezone'))->format('H:i') }}
                da IP {{ Request::ip() }}
            </div>
        @endif
	</div>
</body>
</html>
