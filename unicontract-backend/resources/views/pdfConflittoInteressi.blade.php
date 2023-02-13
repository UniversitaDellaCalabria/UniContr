<?php

use Request;

?>

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
	<h3>DICHIARAZIONE DI ASSENZA DI CONFLITTO DI INTERESSI</h3>
    <p style="text-align: center">
        <small>(ai sensi dell'art 53, comma 14, d.lgs. n. 165/2001)</small>
    </p>
    <br>
	<br>
	<p class="normal">
		@if($pre->anagrafica->sesso == 'M')
			Il sottoscritto
		@else
			La sottoscritta
		@endif
	<b>{{ $pre->user->nameTutorString() }}</b>, in relazione all'incarico
    conferito con <b>{{ $pre->insegnamento->deliberaString() }}</b>
	 dell'Università della Calabria
     </p>

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

    <br>
	<br>
    <p>@if($pre->anagrafica->sesso == 'M')
			Il sottoscritto
		@else
			La sottoscritta
		@endif
        si impegna, altresì, a comunicare tempestivamente eventuali variazioni del
    contenuto della presente dichiarazione e a rendere, nel caso, una nuova dichiarazione sostitutiva.</p>
    <br>
	<br>
    <p></p>@if($pre->anagrafica->sesso == 'M')
			Il sottoscritto
		@else
			La sottoscritta
		@endif
        dichiara inoltre l'insussistenza delle predette situazioni di conflitto e cause
    di incompatibilità sin dal momento del conferimento dell'incarico.</p>
    <br>
	<br>

    <p>F.to<br>
    <b>{{ $pre->user->nameTutorString() }}</b></p>

    <br>
    <br>

    <div class="piepagina">
        Sottoscritto da {{ $pre->user->nameTutorString() }}
        su https://unicontr.unical.it
        il {{ $pre->conflittointeressi->updated_at ? $pre->conflittointeressi->updated_at->format('d/m/Y') : $pre->validazioni->dateSubmitToPrint() }}
        alle {{ $pre->conflittointeressi->updated_at ? $pre->conflittointeressi->updated_at->format('H:i') : $pre->validazioni->hourSubmitToPrint() }}
        da IP {{ Request::ip() }}
    </div>

</body>
</html>
