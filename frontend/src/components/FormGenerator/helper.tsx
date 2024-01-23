import InputText from "./Fields/InputText";
import InputEmail from "./Fields/InputEmail";
import InputCheckbox from "./Fields/InputCheckbox";
import InputPassword from "./Fields/InputPassword";
import InputNumber from "./Fields/InputNumber";
import InputTitle from "./Fields/InputTitle";
import InputRadioButton from "./Fields/InputRadioButton";
import InputSelect from "./Fields/inputSelect";
import InputFile from "./Fields/InputFile";
import InputTextarea from "./Fields/InputTextarea";

import InputDate from "./Fields/InputDate";
import { Field, FieldType } from "@/types/Generals/FormGenerator";
import InputSelectDesing from "./Fields/InputSelectDesing";
import InputSubform from "./Fields/InputSubform";
import InputRecetas from "./Fields/InputRecetas";
import InputInvisite from "./Fields/InputInvisite";
import InputSubTitle from "./Fields/InputSubtitle";
import InputSeparation from "./Fields/InputSeparation";
import InputMedicaments from "./Fields/InputMedicaments";
import InputRoom from "./Fields/InputRoom";

export const getDefaultValues = (fields: Field[], isSubform?: number) => {
  const defaultValues: any = {};

  fields.map((field) => {
    if (field.type !== "title" && field.type !== "separation") {
      defaultValues[field.name] = field.default || "";
    }
  });
  12;

  return defaultValues;
};

export const FieldComponents: FieldType = {
  text: InputText,
  email: InputEmail,
  checkbox: InputCheckbox,
  subForm: InputSubform,
  password: InputPassword,
  number: InputNumber,
  title: InputTitle,
  radioButton: InputRadioButton,
  select: InputSelect,
  file: InputFile,
  textarea: InputTextarea,
  date: InputDate,
  selecDesing: InputSelectDesing,
  recetas: InputRecetas,
  invisible: InputInvisite,
  subtitle: InputSubTitle,
  separation: InputSeparation,
  medicaments: InputMedicaments,
  room: InputRoom,
};

export function isHttp(url: String) {
  return url?.startsWith("http://") || url?.startsWith("https://");
}

async function urlToFile(url: string, mimeType: string) {
  if (typeof url !== "string") return url;
  const httpUrl = isHttp(url)
    ? url
    : `${process.env.NEXT_PUBLIC_BASE_URL}/storage/${url}`;

  const res = await fetch(httpUrl);
  const buf = await res.arrayBuffer();
  const file = new File([buf], url, { type: mimeType });
  return file;
}

export const handleGetFiles = async (urls: string[]) => {
  return new Promise((resolve, reject) => {
    let urlsPromise = urls;

    if (typeof urlsPromise === "string") urlsPromise = [urlsPromise];

    const promiseArray = urlsPromise?.map((url: string) =>
      urlToFile(url, "image/png")
    );

    Promise.all(promiseArray)
      .then((fileArray) => {
        resolve(fileArray);
      })
      .catch((err) => reject(err));
  });
};
