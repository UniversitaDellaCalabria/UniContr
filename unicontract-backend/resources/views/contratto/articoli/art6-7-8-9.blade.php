<h4>Art. 6</h4>
@if($pre->naturaRapporto == "PTG")
{{-- // COLLABORAZIONE GRATUITA --}}
	<p class="normal">
	Per la durata dell’incarico, la copertura assicurativa contro gli infortuni potrà essere richiesta direttamente dal contrattista, mediante produzione della ricevuta del versamento del premio annuo alla struttura didattica ospitante, ovvero da quest’ultima, mediante imputazione del premio annuo al Bilancio dell’Ateneo. L’Università provvede, inoltre, agli adempimenti previsti dall’art. 1, comma 1180, della Legge n. 296/2006 e dal Decreto Interministeriale del Ministero del Lavoro e della Previdenza Sociale del 30.10.2007.
	</p>
@elseif($pre->naturaRapporto == "COCOCO")
{{-- // TC006 / TC007 - COLLABORAZIONE DI NATURA AUTONOMA --}}
	<p class="normal">
	In materia di assicurazione contro gli infortuni sul lavoro e le malattie professionali, a norma dell’art. 55, comma I, della Legge 17.05.1999, n. 144, troveranno applicazione le disposizioni contenute nell’art. 5 “Assicurazione dei lavoratori parasubordinati” del Decreto Legislativo 23.02.2000, n. 38, nell’art. 1, comma 1180 della legge n. 296/2006 e nel Decreto Interministeriale del Ministero del Lavoro e della Previdenza Sociale del 30.10.2007. Il premio assicurativo sarà ripartito nella misura di un terzo a carico del contrattista e di due terzi a carico del committente.
	</p>
@elseif($pre->naturaRapporto == "PRPR")
{{-- // / TC006 - TC007 PRESTAZIONE PROFESSIONALE (PARTITA IVA) --}}
	<p class="normal">
	L’attività oggetto del presente contratto si configura come <b>prestazione professionale</b> e darà luogo a reddito di lavoro autonomo, ai sensi dell’art.53, comma 1, del D.P.R. 917/86, in quanto la prestazione d’opera richiesta rientra nell’oggetto tipico dell’attività professionale esercitata abitualmente.
	</p>
@else
{{-- // PLAO - PRESTAZIONE DI LAVORO AUTONOMO OCCASIONALE OPPURE ALD - ASSIMILATO A LAVORO DIPENDENTE --}}
	<p class="normal">
	<b>***NATURA DEL RAPPORTO NON PREVISTA***</b>
	</p>
@endif

<h4>Art. 7</h4>
@if($pre->naturaRapporto == "PTG")
{{-- // COLLABORAZIONE GRATUITA --}}
	<p class="normal">
	Il presente contratto ha durata annuale e non può, dunque, protrarsi  oltre l’A.A. {{$pre->aa}}. Esso si risolve automaticamente per inadempimento degli obblighi contrattuali. Nei casi in cui si verifichino astensioni particolari, dovute a cause di forza maggiore, si applicheranno le leggi vigenti in materia.
	</p>
@elseif($pre->naturaRapporto == "COCOCO")
{{-- // TC006 / TC007 - COLLABORAZIONE DI NATURA AUTONOMA --}}
	<p class="normal">
	La collaborazione di cui al presente contratto è fiscalmente assimilata al lavoro dipendente, ai sensi dell’art. 50, comma 1°, lettera c-bis) del D.P.R. n. 917/86, così come modificato dall’art. 34 della Legge n. 342/2000. In materia previdenziale troveranno applicazione le disposizioni di cui all’art. 2, comma 26 e segg., della Legge 8.8.1995, n. 335 e successive modificazioni ed integrazioni.
	</p>
@elseif($pre->naturaRapporto == "PRPR")
{{-- // / TC006 - TC007 PRESTAZIONE PROFESSIONALE (PARTITA IVA) --}}
	<p class="normal">
	Per l’incarico conferito, viene riconosciuto {{$pre->genere['str4']}} {{$pre->genere['str5']}}
    un corrispettivo onnicomprensivo forfettario lordo ammontante a € {{$pre->compenso}},
    al netto di IVA e Cassa Previdenza, per come stabilito

    negli atti

    @foreach(explode('#', $pre->dataDelibera) as $data)
        @if(!$loop->first)
            e
        @endif
        del {{explode('#', $pre->emittente)[$loop->index]}}
        @if(explode('#', $pre->numDelibera)[$loop->index] != '' && strtolower(explode('#', $pre->numDelibera)[$loop->index]) != 'null')
        n. {{explode('#', $pre->numDelibera)[$loop->index]}}
        @endif
        del {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data)->format('d/m/Y')}}
    @endforeach

    del {{$pre->dipDocDes}}.

    Il pagamento del corrispettivo sarà effettuato al termine dell’anno accademico,
    previa validazione del registro elettronico da parte del Direttore del Dipartimento.
    In ogni caso, la misura del corrispettivo sarà rapportata al numero di ore effettivamente svolte.
	</p>
@else
{{-- // PLAO - PRESTAZIONE DI LAVORO AUTONOMO OCCASIONALE OPPURE ALD - ASSIMILATO A LAVORO DIPENDENTE --}}
	<p class="normal">
	<b>***NATURA DEL RAPPORTO NON PREVISTA***</b>
	</p>
@endif

<h4>Art. 8</h4>
@if($pre->naturaRapporto == "PTG")
{{-- // COLLABORAZIONE GRATUITA --}}
	<p class="normal">
	{{$pre->genere['str1']}} {{$pre->genere['str5']}} è tenuto ad osservare le disposizioni contenute nel Codice di Comportamento dei dipendenti pubblici dell’Università della Calabria, emanato con D.R. n. 2653 del 23.12.2014, adottato in attuazione del combinato disposto dell’art. 54, comma 5, del D.Lgs n. 165/2001 e del D.P.R. n. 62/2013. La violazione degli obblighi derivanti dal predetto Codice costituisce clausola di risoluzione del presente contratto. In particolare, {{$pre->genere['str1']}} {{$pre->genere['str5']}} dichiara che è consapevole che l’attribuzione delle funzioni cui si procede con il presente contratto consente l’uso dell’eventuale qualificazione di “professore a contratto” - e non di ulteriori titoli accademici (es. “professore”) - esclusivamente entro i limiti di tempo in cui questa è effettivamente svolta. {{$pre->genere['str1']}} {{$pre->genere['str5']}} si impegna, altresì, sotto la propria personale responsabilità, a fare un utilizzo corretto e non indebito del titolo di “professore a contratto” e del nome dell'Università della Calabria. Ogni condotta contraria a quanto detto, oltre a configurare ipotesi di inadempimento contrattuale, sarà perseguita a norma di legge.
	</p>
@elseif($pre->naturaRapporto == "COCOCO")
{{-- // TC006 / TC007 - COLLABORAZIONE DI NATURA AUTONOMA --}}
	<p class="normal">
	Per l’incarico conferito, viene riconosciuto {{$pre->genere['str4']}} {{$pre->genere['str5']}} un corrispettivo onnicomprensivo forfetario lordo ammontante a € {{$pre->compenso}}, al netto degli oneri a carico dell’Amministrazione,
    per come stabilito

    negli atti

    @foreach(explode('#', $pre->dataDelibera) as $data)
        @if(!$loop->first)
            e
        @endif
        del {{explode('#', $pre->emittente)[$loop->index]}}
        @if(explode('#', $pre->numDelibera)[$loop->index] != '' && strtolower(explode('#', $pre->numDelibera)[$loop->index]) != 'null')
        n. {{explode('#', $pre->numDelibera)[$loop->index]}}
        @endif
        del {{Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data)->format('d/m/Y')}}
    @endforeach

    del {{$pre->dipDocDes}}.

    Il pagamento del corrispettivo sarà effettuato al termine dell’anno accademico, previa validazione del registro elettronico da parte del Direttore del Dipartimento. In ogni caso, la misura del corrispettivo sarà rapportata al numero di ore effettivamente svolte.
	</p>
@elseif($pre->naturaRapporto == "PRPR")
{{-- // / TC006 - TC007 PRESTAZIONE PROFESSIONALE (PARTITA IVA) --}}
	<p class="normal">
	La presente prestazione d’opera intellettuale è soggetta al campo applicativo dell’IVA, pertanto, il soggetto titolare di Partita IVA dovrà rilasciare regolare fattura e l’Ateneo avrà il solo obbligo di adempiere, in qualità di sostituto di imposta, al versamento all’Erario delle ritenute d’acconto I.R.P.E.F., senza porre in essere alcun adempimento previdenziale ed assistenziale.
	</p>
@else
{{-- // PLAO - PRESTAZIONE DI LAVORO AUTONOMO OCCASIONALE OPPURE ALD - ASSIMILATO A LAVORO DIPENDENTE --}}
	<p class="normal">
	<b>***NATURA DEL RAPPORTO NON PREVISTA***</b>
	</p>
@endif


<h4>Art. 9</h4>
@if($pre->naturaRapporto == "PTG")
{{-- // COLLABORAZIONE GRATUITA --}}
	<p class="normal">
	Il presente contratto non dà luogo a diritti in ordine all’accesso nei ruoli delle Università e degli Istituti di Istruzione Universitaria Statali.
	</p>
@elseif($pre->naturaRapporto == "COCOCO")
{{-- // TC006 / TC007 - COLLABORAZIONE DI NATURA AUTONOMA --}}
	<p class="normal">
	La presente prestazione d’opera intellettuale è esclusa dal campo applicativo dell’IVA, in quanto configurata come lavoro assimilato a quello dipendente, giusto art. 7 del presente contratto.
	</p>
@elseif($pre->naturaRapporto == "PRPR")
{{-- // / TC006 - TC007 PRESTAZIONE PROFESSIONALE (PARTITA IVA) --}}
	<p class="normal">
	Il presente contratto ha durata annuale e non può, dunque, protrarsi oltre l’A.A. {{$pre->aa}}. Esso si risolve automaticamente per inadempimento degli obblighi contrattuali. Nei casi in cui si verifichino astensioni particolari, dovute a cause di forza maggiore, si applicheranno le leggi vigenti in materia.
	</p>
@else
{{-- // PLAO - PRESTAZIONE DI LAVORO AUTONOMO OCCASIONALE OPPURE ALD - ASSIMILATO A LAVORO DIPENDENTE --}}
	<p class="normal">
	<b>***NATURA DEL RAPPORTO NON PREVISTA***</b>
	</p>
@endif
