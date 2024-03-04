export interface IMedicament {
  name: string;
  id: string;
  ingredient?: string;
  vnombreproducto?: string;
  vnombresal?: string;
  uicodproducto?: string;
  new?: boolean;
  familia?: string;
  via?: string;
  clasificacionsa?: string;
  tipoproducto?: string;
  information?: string;
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
  temp: string;
  weight: string;
  height: string;
  pressure: string;
  saturation: string;
  ppm: string;
  diagnostic: string;
  add: string;
  room_id: string;
}
