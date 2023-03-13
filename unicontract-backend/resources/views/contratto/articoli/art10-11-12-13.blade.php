<h4>Art. 10</h4>
@if($pre->naturaRapporto == "PTG")
{{-- // COLLABORAZIONE GRATUITA --}}
	<p class="normal">
	Per quanto non espressamente disposto, il presente contratto è regolato dagli articoli del Libro V, Tit. III del Codice Civile. In caso di inadempimento, si applicano le disposizioni contenute nel Libro IV, Tit. II, Capo XIV, del Codice Civile.
	</p>
@elseif($pre->naturaRapporto == "COCOCO")
{{-- // TC006 / TC007 - COLLABORAZIONE DI NATURA AUTONOMA --}}
	<p class="normal">
	Per i soggetti non residenti in Italia, qualora gli stessi non optino per il pagamento delle tasse all’estero, sarà operata sul corrispettivo una ritenuta a titolo d’imposta nella misura del 30%, ai sensi dell’art. 24, comma 1-ter) del D.P.R. n. 600/73, così come modificato dall’art. 34 della Legge n. 342/2000.
	</p>
@elseif($pre->naturaRapporto == "PRPR")
{{-- // / TC006 - TC007 PRESTAZIONE PROFESSIONALE (PARTITA IVA) --}}
	<p class="normal">
	{{$pre->genere['str0']}} {{$pre->genere['str5']}} è tenut{{$pre->genere['str2']}} ad osservare le disposizioni contenute nel Codice di Comportamento dei dipendenti pubblici dell’Università della Calabria, emanato con D.R. n. 2653 del 23.12.2014, adottato in attuazione del combinato disposto dell’art. 54, comma 5, del D.Lgs n. 165/2001 e del D.P.R. n. 62/2013. La violazione degli obblighi derivanti dal predetto Codice costituisce clausola di risoluzione del presente contratto. In particolare, {{$pre->genere['str1']}} {{$pre->genere['str5']}} dichiara che è consapevole che l’attribuzione delle funzioni cui si procede con il presente contratto consente l’uso dell’eventuale qualificazione di "professore a contratto" - e non di ulteriori titoli accademici (es. "professore") - esclusivamente entro i limiti di tempo in cui questa è effettivamente svolta. {{$pre->genere['str0']}} {{$pre->genere['str5']}} si impegna, altresì, sotto la propria personale responsabilità, a fare un utilizzo corretto e non indebito del titolo di "professore a contratto" e del nome dell'Università della Calabria. Ogni condotta contraria a quanto detto, oltre a configurare ipotesi di inadempimento contrattuale, sarà perseguita a norma di legge.
	</p>
@else
{{-- // PLAO - PRESTAZIONE DI LAVORO AUTONOMO OCCASIONALE OPPURE ALD - ASSIMILATO A LAVORO DIPENDENTE --}}
	<p class="normal">
	<b>***NATURA DEL RAPPORTO NON PREVISTA***</b>
	</p>
@endif

@if($pre->naturaRapporto == "PTG")
{{-- // COLLABORAZIONE GRATUITA --}}
	<p class="normal">

	</p>
@elseif($pre->naturaRapporto == "COCOCO")
{{-- // TC006 / TC007 - COLLABORAZIONE DI NATURA AUTONOMA --}}
<h4>Art. 11</h4>
	<p class="normal">
	Il presente contratto ha durata annuale e non può, dunque, protrarsi oltre l’A.A. {{$pre->aa}}. Esso si risolve automaticamente per inadempimento degli obblighi contrattuali. Nei casi in cui si verifichino astensioni particolari, dovute a cause di forza maggiore, si applicheranno le leggi vigenti in materia.
	</p>
@elseif($pre->naturaRapporto == "PRPR")
{{-- // / TC006 - TC007 PRESTAZIONE PROFESSIONALE (PARTITA IVA) --}}
<h4>Art. 11</h4>
	<p class="normal">
	Il presente contratto non dà luogo a diritti in ordine all’accesso nei ruoli delle Università e degli Istituti di Istruzione Universitaria Statali.
	</p>
@else
{{-- // PLAO - PRESTAZIONE DI LAVORO AUTONOMO OCCASIONALE OPPURE ALD - ASSIMILATO A LAVORO DIPENDENTE --}}
<h4>Art. 11</h4>
	<p class="normal">
	<b>***NATURA DEL RAPPORTO NON PREVISTA***</b>
	</p>
@endif

@if($pre->naturaRapporto == "PTG")
{{-- // COLLABORAZIONE GRATUITA --}}
	<p class="normal">
	</p>
@elseif($pre->naturaRapporto == "COCOCO")
{{-- // TC006 / TC007 - COLLABORAZIONE DI NATURA AUTONOMA --}}
<h4>Art. 12</h4>
	<p class="normal">
	{{$pre->genere['str0']}} {{$pre->genere['str5']}} è tenut{{$pre->genere['str2']}} ad osservare le disposizioni contenute nel Codice di Comportamento dei dipendenti pubblici dell’Università della Calabria, emanato con D.R. n. 2653 del 23.12.2014, adottato in attuazione del combinato disposto dell’art. 54, comma 5, del D.Lgs n. 165/2001 e del D.P.R. n. 62/2013. La violazione degli obblighi derivanti dal predetto Codice costituisce clausola di risoluzione del presente contratto. In particolare, {{$pre->genere['str1']}} {{$pre->genere['str5']}} dichiara che è consapevole che l’attribuzione delle funzioni cui si procede con il presente contratto consente l’uso dell’eventuale qualificazione di “professore a contratto” - e non di ulteriori titoli accademici (es. "professore") - esclusivamente entro i limiti di tempo in cui questa è effettivamente svolta. {{$pre->genere['str0']}} {{$pre->genere['str5']}} si impegna, altresì, sotto la propria personale responsabilità, a fare un utilizzo corretto e non indebito del titolo di “professore a contratto” e del nome dell'Università della Calabria. Ogni condotta contraria a quanto detto, oltre a configurare ipotesi di inadempimento contrattuale, sarà perseguita a norma di legge.
	</p>
@elseif($pre->naturaRapporto == "PRPR")
{{-- // / TC006 - TC007 PRESTAZIONE PROFESSIONALE (PARTITA IVA) --}}
<h4>Art. 12</h4>
	<p class="normal">
	Per quanto non espressamente disposto, il presente contratto è regolato dagli articoli del Libro V, Tit. III del Codice Civile. In caso di inadempimento, si applicano le disposizioni contenute nel Libro IV, Tit. II, Capo XIV, del Codice Civile.
	</p>
@else
{{-- // PLAO - PRESTAZIONE DI LAVORO AUTONOMO OCCASIONALE OPPURE ALD - ASSIMILATO A LAVORO DIPENDENTE --}}
<h4>Art. 12</h4>
	<p class="normal">
	<b>***NATURA DEL RAPPORTO NON PREVISTA***</b>
	</p>
@endif

@if($pre->naturaRapporto == "COCOCO")
{{-- // TC006 / TC007 - COLLABORAZIONE DI NATURA AUTONOMA --}}
<h4>Art. 13</h4>
	<p class="normal">
	Il presente contratto non dà luogo a diritti in ordine all’accesso nei ruoli delle Università e degli Istituti di Istruzione Universitaria Statali.
	</p>
@endif


@if($pre->naturaRapporto == "COCOCO")
{{-- // TC006 / TC007 - COLLABORAZIONE DI NATURA AUTONOMA --}}
<h4>Art. 14</h4>
	<p class="normal">
	Per quanto non espressamente disposto, il presente contratto è regolato dagli articoli del Libro V, Tit. III del Codice Civile. In caso di inadempimento, si applicano le disposizioni contenute nel Libro IV, Tit. II, Capo XIV, del Codice Civile.
	</p>
@endif
