export interface ISpecialization {
  name: string | null;
  identification: string | null;
  university: string | null;
  file: any | null;
  logo?: any | null;
  id?: number | null;
}

export interface IRoom {
  name: string | null;
  zip: string | null;
  street: string | null;
  colony: string | null;
  state: string | null;
  delegation: string | null;
  n_exterior: string | null;
  n_interior: string | null;
  address: string | null;
  phone: string | null;
  file: any | null;
  logo?: any | null;
  id?: number | null;
  design: any | null;
}

export interface IForm1 {
  first_name: string | null;
  last_name1: string | null;
  last_name2: string | null;
  email: string | null;
  phone1: string | null;
  phone2: string | null;
  gender: string | null;
  fesa: string | null;
  password: string | null;
  confirmPassword: string | null;
}
export interface IForm2 {
  specializations: ISpecialization[] | null;
}

export interface IForm3 {
  rooms: IRoom[] | null;
}

export interface ExternalClient {
  idCX: string | null;
  clienteEcommerce: boolean | null;
}

export interface IRegisterPayload
  extends IForm1,
    IForm2,
    IForm3,
    ExternalClient {}

export interface ILoginPayload {
  email: string | null;
  password: string | null;
}

export interface IForgotPayload {
  email: string | null;
}
export interface IRecoverPayload {
  password: string | null;
  password_confirmation: string | null;
  email?: string | null;
  token?: string | null;
}
