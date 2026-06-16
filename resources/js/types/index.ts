export type QueryParams = Record<string, string | number | boolean | undefined>;

export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

export interface Paginated<T> {
  data: T[];
  meta?: unknown;
  links?: unknown;
}

export type ResourceResponse<T> = ApiResponse<T>;
export type PaginatedResourceResponse<T> = ApiResponse<Paginated<T>>;

export type Gender = string;

export interface University {
  id: number;
  name: string;
  alpha_two_code?: string;
  country?: string;
}

export interface Patient {
  id: number;
  first_name: string;
  last_name1: string;
  last_name2: string;
  email: string;
  phone: string | string[];
  gender: string;
  birth_date: string | null;
}

export type PatientPayload = Omit<Patient, 'id'>;
export type PatientForm = Omit<PatientPayload, 'phone'> & { phone: string[] };

export interface Profile {
  first_name: string;
  last_name1: string;
  last_name2: string;
  email: string;
  phone: string | string[];
  gender: string;
  rooms?: Room[];
  specialties?: Specialty[];
}

export type ProfilePayload = Omit<Profile, 'rooms' | 'specialties'>;
export type ProfileForm = Omit<ProfilePayload, 'phone'> & { phone: string[] };

export interface Room {
  id: number;
  name: string;
  zip: string;
  street: string;
  colony: string;
  state: string;
  delegation: string;
  n_exterior: string;
  n_interior: string;
  address?: string;
  phone: string | string[];
  fav: boolean;
  auto_email: boolean;
  auto_whatsapp: boolean;
}

export type RoomPayload = Omit<Room, 'id' | 'address'>;
export type RoomForm = Omit<RoomPayload, 'phone'> & { phone: string[] };

export interface Specialty {
  id: number;
  name: string;
  identification: string;
  university: string;
}

export type SpecialtyPayload = Omit<Specialty, 'id'>;

export interface Medicament {
  id: number;
  name: string;
}

export interface PrescriptionMedicament {
  id: number;
  name: string;
  dosage: string;
  frequency: string;
  duration: string;
}

export interface PrescriptionMedicamentInput {
  medicament_id: string | number;
  dosage: string;
  frequency: string;
  duration: string;
}

export interface Prescription {
  id: number;
  user_id: number;
  patient_id: number;
  room_id: number;
  specialty_id: number;
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
  status: string;
  pretty_status?: string;
  room?: Room;
  patient?: Patient;
  specialty?: Specialty;
  medicaments?: PrescriptionMedicament[];
}

export interface PrescriptionPayload {
  patient_id: string | number;
  room_id: string | number;
  specialty_id: string | number;
  temp: string;
  weight: string;
  height: string;
  pressure: string;
  saturation: string;
  ppm: string;
  allergy: string;
  diagnostic: string;
  diet: string;
  comments: string;
  medicament_data: PrescriptionMedicamentInput[];
}

export interface Credentials {
  email: string;
  password: string;
}

export interface LoginResponse {
  token: string;
  profile: Profile;
}
