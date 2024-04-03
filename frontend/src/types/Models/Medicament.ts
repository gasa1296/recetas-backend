export interface IMedicament {
  name?: string;
  id?: string;
  ingredient?: string;
  vnombreproducto?: string;
  vnombresal?: string;
  uicodproducto?: string;
  new?: boolean;
  familia?: string;
  via?: string;
  group?: string;
  type?: string;
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
  temp: string | number;
  weight: string | number;
  height: string | number;
  pressure: string | number;
  saturation: string | number;
  ppm: string | number;
  diagnostic: string;
  add: string;
  room_id: string;
}
