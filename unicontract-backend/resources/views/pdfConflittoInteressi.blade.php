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

    @if ($pre->conflittointeressi)
        @if ($pre->conflittointeressi->flag_controll && $pre->conflittointeressi->flag_quota)
            <p class="normal">- di avere il controllo e di possedere una quota significativa di partecipazione
            finanziaria in enti o persone giuridiche in situazioni di conflitto di interesse con l’Università della Calabria;</p>
        @elseif ($pre->conflittointeressi->flag_controll &! $pre->conflittointeressi->flag_quota)
            <p class="normal">- di avere il controllo e di non possedere una quota significativa di partecipazione
            finanziaria in enti o persone giuridiche in situazioni di conflitto di interesse con l’Università della Calabria;</p>
        @elseif (!$pre->conflittointeressi->flag_controll && $pre->conflittointeressi->flag_quota)
            <p class="normal">- di non avere il controllo e di possedere una quota significativa di partecipazione
            finanziaria in enti o persone giuridiche in situazioni di conflitto di interesse con l’Università della Calabria;</p>
        @elseif (!$pre->conflittointeressi->flag_controll &! $pre->conflittointeressi->flag_quota)
            <p class="normal">- di non avere il controllo e di non possedere una quota significativa di partecipazione
            finanziaria in enti o persone giuridiche in situazioni di conflitto di interesse con l’Università della Calabria;</p>
        @endif

        @if ($pre->conflittointeressi->flag_rappext)
            <p class="normal">- avere rapporti esterni di lavoro con enti di formazione e di ricerca
            potenzialmente concorrenti con l’Università della Calabria;</p>
        @else
            <p class="normal">- non avere rapporti esterni di lavoro con enti di formazione e di ricerca
            potenzialmente concorrenti con l’Università della Calabria;</p>
        @endif

        @if($pre->conflittointeressi->flag_contrast)
            <p class="normal">- di svolgere attività che contrastano realmente o potenzialmente con l’interesse, non solo economico, dell’Università della Calabria.</p>
        @else
            <p class="normal">- di non svolgere attività che contrastano realmente o potenzialmente con l’interesse, non solo economico, dell’Università della Calabria.</p>
        @endif
    @endif

</body>
</html>
