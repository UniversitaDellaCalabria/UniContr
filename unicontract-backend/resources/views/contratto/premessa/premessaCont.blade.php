<p class="normal">
	Premesso che, in attuazione dell’art. 23 della Legge n. 240 del 30.12.2010, l’Università della Calabria ha adottato, con D.R. n. 1961 del 24.09.2012, il “Regolamento per il conferimento di incarichi di insegnamento nei Corsi di studio dell’Università della Calabria”
</p>
<h4>tra</h4>
<p class="normal">
	la stessa Università della Calabria, Codice Fiscale 80003950781, in seguito indicata Università, rappresentata legalmente dal <b>Prof. Nicola LEONE</b>, nato a Diamante (CS) il 28.02.1963, Rettore pro-tempore, domiciliato per la sua carica presso l’Università
</p>
<h4>e</h4>
<p class="normal">
	{{ $pre->user->nameTutorString() }}, {{ $pre->datiAnagraficaString() }},
	Codice Fiscale {{ $pre->user->cf }}, {{ $pre->anagrafica->datiResidenza() }},
    viene stipulato il presente contratto di prestazione d’opera intellettuale
	@if($pre->isComma1Gratuito())
		a <b>titolo gratuito, nei limiti del 5% dell’organico dei professori e ricercatori di ruolo del Dipartimento, ai sensi dell’art. 23, comma 1, della legge n. 240/2010</b>,
	@elseif($pre->isComma1Retribuito())
		a <b>titolo retribuito, ai sensi dell’art. 23, comma 1, della Legge n. 240/2010</b>,
	@elseif($pre->isComma2Retribuito())
		a <b>titolo retribuito, ai sensi dell’art. 23, comma 2, della Legge n. 240/2010</b>,
	@else
		**** TIPOLOGIA DI CONTRATTO NON PREVISTA ****
	@endif
	valevole esclusivamente per l’<b>A.A. {{$pre->aa}}</b>.
</p>
