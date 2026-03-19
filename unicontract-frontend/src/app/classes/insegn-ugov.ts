import { InsegnUgovInterface } from './../interface/insegn-ugov';

export class InsegnUgov implements InsegnUgovInterface {
    coper_id: number;
    dip_desc_ita: string;
    cds_desc_ita: string;
    aa_off_id: string;
    ana_af_cod: string;
    ana_af_desc_ita: string;
    sett_cod: string;
    sett_des: string;
    peso: string;
    tipo_periodo_did_desc_ita: string;
    doc_matricola: string;
    doc_cognome: string;
    doc_nome: string;
    sesso: string;
    cod_fis: string;
    doc_ruolo: string;
    coper_peso: string;
    ore: number;
    ore_desc: string;
    data_inizio_contratto: string;
    data_fine_contratto: string;
    compenso: string;
    tipo_coper_cod: string;
    tipo_atto_desc_ita: string;
    tipo_emittente_desc_ita: string;
    motivo_atto_cod: string;
    numero_atto: string;
    data_atto: string;
    contatore_insegnamenti?: number;
    tipo_corso_desc_ita: string;
    anno_corso: string;
    doc_aff_org: string;
    doc_aff_org_ita: string;

    constructor() {
        this.coper_id = 0;
        this.dip_desc_ita = '';
        this.cds_desc_ita = '';
        this.aa_off_id = '';
        this.ana_af_cod = '';
        this.ana_af_desc_ita = '';
        this.sett_cod = '';
        this.sett_des = '';
        this.peso = '';
        this.tipo_periodo_did_desc_ita = '';
        this.doc_matricola = '';
        this.doc_cognome = '';
        this.doc_nome = '';
        this.sesso = '';
        this.cod_fis = '';
        this.doc_ruolo = '';
        this.coper_peso = '';
        this.ore = 0;
        this.ore_desc = '';
        this.data_inizio_contratto = '';
        this.data_fine_contratto = '';
        this.compenso = '';
        this.tipo_coper_cod = '';
        this.tipo_atto_desc_ita = '';
        this.tipo_emittente_desc_ita = '';
        this.motivo_atto_cod = '';
        this.numero_atto = '';
        this.data_atto = '';
        this.tipo_corso_desc_ita = '';
        this.anno_corso = ''; // GDA todo // da ODS_L1_MOD_PDS_OFF
        this.doc_aff_org = '';
        this.doc_aff_org_ita = '';
    }

}
