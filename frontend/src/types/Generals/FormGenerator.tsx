import { StaticImageData } from "next/image";

export interface Field {
  label?: string;
  secondLabel?: string;
  name: string;
  required?: boolean;
  type: FieldTypeString;
  default?: any;
  options?: { label: string; value: string | number }[];
  maxFile?: number;
  ModalComponent?: any;
  Component?: any;
  createTitle?: string;
  selectTitle?: string;
  subLabel?: string;
  limitDays?: number;
  disabledStyle?: boolean;
  marks?: any;
  maxRange?: number;
  customChange?: any;
  minDate?: string;
  maxDate?: string;
  tooltip?: { title: string; content: string };
  disabled?: boolean;
  visible?: string;
  inputType?: string;
  validate?: (value: string, payload: any) => void;
  handleChange?: (value: any) => void;
  setError?: any;
  register?: any;
  error?: any;
  setValue?: any;
  watch?: any;
  width?: number;
  form?: any;
  subFormKey?: string;
  buttonAddText?: string;
  notFirstTitle?: boolean;
  recetasOptions?: { image: StaticImageData; value: string }[];
  externalError?: any;
  minLength?: number;
  max?: number;
  min?: number;
}

export interface FieldType {
  text: any;
  email: any;
  checkbox: any;
  password: any;
  number: any;
  subForm: any;
  title: any;
  radioButton: any;
  select: any;
  file: any;
  textarea: any;
  date: any;
  selecDesing: any;
  recetas: any;
  invisible: any;
  subtitle: any;
  separation: any;
  medicaments: any;
  room: any;
}
export type FieldTypeString =
  | "text"
  | "email"
  | "checkbox"
  | "password"
  | "number"
  | "title"
  | "radioButton"
  | "select"
  | "subForm"
  | "file"
  | "textarea"
  | "date"
  | "selecDesing"
  | "invisible"
  | "recetas"
  | "subtitle"
  | "separation"
  | "medicaments"
  | "room";

export interface SelectedItems {
  type: "cause" | "prize";
  name: string;
  description: string;
  goal: number;
  value: number;
  image?: string;
  id: number;
}
