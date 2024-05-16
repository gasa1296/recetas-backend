import { IMedicament } from "./Medicament";
import { IPacient } from "./Pacient";

export interface IRecipes {
  id: string;
  temp: number;
  weight: number;
  height: number;
  pressure: number;
  saturation: number;
  ppm: number;
  diagnostic: string;
  add: string;
  patient_id: string;
  room_id: string;
  user_id: string;
  medicaments: IMedicament[];
  patient?: IPacient;
  status?: number;
  created_at: string;
  room?: any;
  add_med?: string;
  code?: string;
  document_id?: string;
}
