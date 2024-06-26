import { AnagraficaLocalInterface } from './../interface/anagrafica-local.interface';

export class AnagraficaLocal implements AnagraficaLocalInterface {
    sesso: string;
    nazione_nascita: string;
    comune_nascita: string;
    provincia_nascita: string;
    data_nascita: string;
    cf_coniuge: string;
    stato_civile: string;
    titolo_studio: string;
    nazione_residenza: string;
    comune_residenza: string;
    provincia_residenza: string;
    cap_residenza: string;
    indirizzo_residenza: string;
    civico_residenza: string;
    data_variazione_residenza: string;
    comune_fiscale: string;
    provincia_fiscale: string;
    cap_fiscale: string;
    indirizzo_fiscale: string;
    civico_fiscale: string;
    data_variazione_dom_fiscale: string;
    comune_comunicazioni: string;
    provincia_comunicazioni: string;
    cap_comunicazioni: string;
    indirizzo_comunicazioni: string;
    civico_comunicazioni: string;
    telefono_abitazione: string;
    telefono_cellulare: string;
    telefono_ufficio: string;
    fax: string;
    email: string;
    email_privata: string;
    cf: string;

    constructor() {
        this.sesso = '';
        this.nazione_nascita = '';
        this.comune_nascita = '';
        this.provincia_nascita = '';
        this. data_nascita = '';
        this.cf_coniuge = '';
        this.stato_civile = '';
        this. titolo_studio = '';
        this.nazione_residenza = ''; //memorizzata la cittadinanza
        this.comune_residenza = '';
        this.provincia_residenza = '';
        this.cap_residenza = '';
        this.indirizzo_residenza = '';
        this.civico_residenza = '';
        this.data_variazione_residenza = '';
        this.comune_fiscale = '';
        this.provincia_fiscale = '';
        this.cap_fiscale = '';
        this.indirizzo_fiscale = '';
        this.civico_fiscale = '';
        this.data_variazione_dom_fiscale = '';
        this.comune_comunicazioni = '';
        this.provincia_comunicazioni = '';
        this.cap_comunicazioni = '';
        this.indirizzo_comunicazioni = '';
        this.civico_comunicazioni = '';
        this.telefono_abitazione = '';
        this.telefono_cellulare = '';
        this.telefono_ufficio = '';
        this.fax = '';
        this.email = '';
        this.email_privata = '';
    }
}
