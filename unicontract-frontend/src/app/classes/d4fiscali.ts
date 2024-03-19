import { D4FiscaliInterface } from './../interface/d4fiscali.interface';

export class D4Fiscali implements D4FiscaliInterface {
    id: number;
    flag_aliquota_irpef_scaglioni: boolean;
    percentuale_aliquota_irpef: string;
    flag_detrazioni: boolean;
    detrazioni: string;
    reddito: string;
    flag_bonus_renzi: boolean;

    constructor() {
        this.id = 0;
        this.flag_aliquota_irpef_scaglioni = false;
        this.percentuale_aliquota_irpef = '';
        this.flag_detrazioni = false;
        this.detrazioni = '';
        this.reddito = '';
        this.flag_bonus_renzi = false;
    }
}
