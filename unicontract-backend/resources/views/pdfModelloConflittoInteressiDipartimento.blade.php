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
    <h3>ATTESTAZIONE DELL’AVVENUTA VERIFICA DELL’INSUSSISTENZA DI SITUAZIONI, ANCHE POTENZIALI, DI CONFLITTO DI INTERESSE</h3>
    <p class="small">
    (ai sensi dell’art. 53, comma 14 del D.Lgs.  n. 165/2001)
    </p>
    <br>
    <p class="normal">
    Il/La sottoscritto/a {{ $nome_direttore }}
    in qualità di Direttore del Dipartimento di {{$pre->dipartimento}}
    dell'Università della Calabria
    </p>
    <br>
    <p class="normal">
    VISTO l’art. 53 D. Lgs. n. 165/2001, come modificato dalla legge n. 190/2012, che prevede che il conferimento di ogni incarico sia subordinato all’avvenuta verifica dell’insussistenza di situazioni, anche potenziali, di conflitti di interesse;
    </p>
    <br>
    <p class="normal">
    VISTO il curriculum, nonché la dichiarazione di assenza di conflitto di interessi resa, ai sensi dell’art. 53, comma 14, del D. Lgs. n. 165/2001, da {{ $pre->user->nameTutorString() }} in relazione al seguente incarico:
    <br>
    <br>
    Incarico di insegnamento {{ $pre->insegnamentoDescr }} - SSD {{ $pre->settore }} - Corso di {{ $pre->tipoCorsoDes }} in {{ $pre->cdl }} - Anno di corso {{ $pre->annoCorso }} - Semestre {{ $pre->periodo }} - per {{ $pre->ore }} ore complessive ({{ $pre->oreDesc }}) - A.A. {{$pre->aa}}
    <br>
    <br>
    stabilito con {{ $pre->insegnamento->deliberaString() }}
    </p>

    <h4>ATTESTA</h4>
    <p class="normal">
    l’avvenuta verifica dell’insussistenza di situazioni, anche potenziali, di conflitto di interesse ai sensi dell’art. 53 del D. Lgs. n. 165/2001, come modificato dalla Legge n. 190/2012.
    <br><br>
    La presente attestazione è pubblicata nella sezione "Consulenti e collaboratori" del "Portale Amministrazione Trasparente" dell’Università della Calabria.
    </p>

     <br>
     <p class="small">
     RENDE (CS), {{ date('d/m/Y') }}
     </p>
     <br>
     <p class="normal">
     Il Direttore del Dipartimento
     </p>

     <br>
     <br>
     <div class="piepagina">
        Documento firmato digitalmente ai sensi del Codice dell'Amministrazione Digitale e norme ad esso connesse
     </div>
    </body>
</html>
