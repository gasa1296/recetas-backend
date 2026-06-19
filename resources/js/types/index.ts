export type QueryParams = Record<string, string | number | boolean | undefined>;

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
  meta?: unknown;
  links?: unknown;
}

export type Gender = string;

export interface University {
  id: number;
  name: string;
  alpha_two_code?: string;
  country?: string;
}

export interface Patient {
  id?: number;
  first_name: string;
  last_name: string;
  identification: string;
  email: string;
  phone?: string[];
  gender: string;
  birth_date: string | null;
}

export interface Profile {
  first_name: string;
  last_name: string;
  identification: string;
  email: string;
  phone: string[];
  rooms?: Room[];
  specialties?: Specialty[];
}

export interface Room {
  id?: number;
  name: string;
  identification: string;
  zip: string;
  address?: string;
  phone: string[];
}

export interface Specialty {
  id?: number;
  name: string;
  identification: string;
}

export interface Medicament {
  id: number;
  active_ingredient: string;
  type: string;
  group: string;
}

export interface PrescriptionMedicament {
  id: number;
  active_ingredient?: string;
  type?: string;
  group?: string;
  dosage: string;
  frequency: string;
  duration: string;
  medicament_quantity?: number;
  medicament_quantity_letters?: string;
}

export interface Prescription {
  id?: number;
  patient_id?: number;
  room_id?: number;
  temp: string | null;
  weight: string | null;
  height: string | null;
  pressure: string | null;
  saturation: string | null;
  ppm: string | null;
  allergy: string | null;
  diagnostic: string | null;
  diet: string | null;
  comments: string | null;
  status?: number;
  pretty_status?: string;
  room?: Room;
  patient?: Patient;
  medicaments?: PrescriptionMedicament[];
}

export interface Credentials {
  email: string;
  password: string;
}

export interface LoginResponse {
  token: string;
  profile: Profile;
}
