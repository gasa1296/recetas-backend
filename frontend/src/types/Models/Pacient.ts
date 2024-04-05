export interface IPacient {
  name: string;
  email: string;
  id: string;
  last_name1: string;
  last_name2: string;
  first_name: string;
  phone1: string;
  phone2: string;
  age: string;
  gender: string;
  prescriptions: any;
  birth_date: string;
  code?: string;
}

export interface IDefaultPacient {
  label: string;
  value: string;
}
