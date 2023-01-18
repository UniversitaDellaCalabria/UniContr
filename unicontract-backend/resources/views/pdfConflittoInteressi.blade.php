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
		font-family: 'Times New Roman', Times, serif;
		font-size: 14pt;
        text-rendering: geometricPrecision;
      }
	  h3 {
		  text-align: center
	  }
	  .normal {
		font-family: 'Times New Roman', Times, serif;
		font-size: 14pt;
        text-rendering: geometricPrecision;
 		line-height: 1.5;
		text-align: justify
	  }
	  .piepagina{
		font-size: 8pt;
		font-style: italic;
		font-family: Arial, Helvetica, sans-serif;
		text-rendering: geometricPrecision;
		text-align: justify;
	  }
	</style>
</head>
<body>
	<h3>Dichiarazione ai sensi dell'art. 15, comma 1, lett. c, del D.Lgs. n. 33/2013 e s.m.i.</h3>
	<p class="normal">
		@if($pre->anagrafica->sesso == 'M')
			Il sottoscritto
		@else
			La sottoscritta
		@endif
	{{ $pre->user->nameTutorString() }} {{ $pre->anagrafica->datiAnagraficaString() }}, C.F.
	{{ $pre->user->cf }}, in relazione all'incarico conferito con {{ $pre->insegnamento->deliberaString() }}
	 dell'Università della Calabria,
     consapevole delle sanzioni penali previste dall'art. 76 del D.P.R. 445/2000, per le ipotesi
	di falsità in atti e di dichiarazioni mendaci ivi indicate, ai sensi e per gli effetti del citato D.P.R. n.
	445/2000, e di quanto disposto dall'art. 15, comma 1, lettera c, del D.Lgs 33/2013 e s.m.i., sotto la
	propria responsabilità</p>

	<h3>DICHIARA</h3>

	test

</body>
</html>
