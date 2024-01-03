import { StaticImageData } from "next/image";

export interface Field {
    label?: string;
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
    validate?: (value: string, payload: any) => void;
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
    | "recetas";

export interface SelectedItems {
    type: "cause" | "prize";
    name: string;
    description: string;
    goal: number;
    value: number;
    image?: string;
    id: number;
}
