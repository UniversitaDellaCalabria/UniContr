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
	</style>
</head>
<body class="@if ($type=='CONTR_BOZZA') bozza @endif">


  <div class="page">

	<h4>UNIVERSITA' DELLA CALABRIA</h4>


	@include( 'contratto.premessa.premessaCont', $pre)
    @include( 'contratto.articoli.art1-2-3-4-5', $pre)
    @include( 'contratto.articoli.art6-7-8-9', $pre)
    @include( 'contratto.articoli.art10-11-12-13', $pre)

    <br>
    <p class="small">
    RENDE, (data ultima sottoscrizione digitale in ordine cronologico attestata dalla marcatura temporale)
    </p>
    <br>
    <p class="normal">
    Il Rettore Nicola Leone
    </p>
    <br>
    <p class="normal">
    {{$pre->genere['str0']}} {{$pre->genere['str5']}} {{ $pre->user->nameTutorString() }}
    </p>
    </div>
    {{-- INFORMATIVA SULLA PRIVACY --}}
    <div class="page ">
    <div class="logo" ></div>
        @include( 'contratto.codice_comportamento', $pre)
		@include( 'contratto.informativa', $pre)
		@include( 'contratto.piao', $pre)
    </div>
</body>
</html>
