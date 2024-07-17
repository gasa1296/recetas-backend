import * as yup from "yup";

export const PacientSchema = yup.object().shape({
  first_name: yup.string().trim().required("El campo Nombre(s) es obligatorio"),
  last_name1: yup
    .string()
    .trim()
    .required("El campo Apellido Paterno es obligatorio"),
  last_name2: yup
    .string()
    .trim()
    .required("El campo Apellido Materno es obligatorio"),
  email: yup
    .string()
    .email("Debe ser un correo electrónico válido")
    .required("El campo Correo electrónico es obligatorio"),
  phone1: yup
    .array()
    .of(
      yup
        .string()
        .required("Debe ingresar al menos un número de teléfono celular")
    )
    .min(1, "Debe tener al menos un número de teléfono celular")
    .required("El campo Teléfono celular es obligatorio")
    .test("phone1", "No puede haber números duplicados", (array) => {
      const phoneSet = new Set(array);
      return phoneSet.size === array.length;
    }),
  phone2: yup.string().ensure().nullable(),
  gender: yup.string().ensure().nullable(),
  birth_date: yup
    .string()
    .required("El campo Correo electrónico es obligatorio"),
});
