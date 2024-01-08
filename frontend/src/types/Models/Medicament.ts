export interface IMedicament {
    name: string;
    id: string;
    ingredient?: string;
    vnombreproducto?: string;
    vnombresal?: string;
    uicodproducto?: string;
    new?: boolean;
}

export interface IMedicamentDefault {
    label: string;
    value: string;
}

export interface INewMedicament {
    name: string;
    ingredient: string;
}

export interface IConfirmRecipForm {
    temperature: string;
    weight: string;
    size: string;
    pressure: string;
    saturation: string;
    rate: string;
    diagnostic: string;
    indications: string;
}
