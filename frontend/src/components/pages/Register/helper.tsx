import { SpecializationSchema } from "@/utils/ValidationSchema/SpecializationSchema";
import * as yup from "yup";
export const form1Schema = yup.object().shape({
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
  gender: yup.string().ensure().nullable(), // Este campo no es requerido.
  fesa: yup.string().trim().required("El campo Código FESA es obligatorio"),
  password: yup.string().required("El campo Contraseña es obligatorio"),
  confirmPassword: yup
    .string()
    .required("Debe confirmar la contraseña")
    .oneOf([yup.ref("password")], "Las contraseñas deben coincidir"),
});
export const form1ProfileSchema = yup.object().shape({
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
  gender: yup.string().ensure().nullable(), // Este campo no es requerido.
  fesa: yup.string().trim().required("El campo Código FESA es obligatorio"),
});

function isRoomsArray(value: any) {
  return (
    (Array.isArray(value) && value.length > 1) ||
    (typeof value === "object" &&
      Array.isArray(value.rooms) &&
      value.rooms.length > 1)
  );
}

export const RoomArraySchema = yup.object().shape({
  rooms: yup
    .array()
    .of(
      yup.lazy((value, context) => {
        let isFirstRoom;
        if (isRoomsArray(context.parent)) isFirstRoom = false;
        else isFirstRoom = true;

        return yup.object().shape({
          name: isFirstRoom
            ? yup.string()
            : yup.string().required("El nombre del consultorio es requerido"),

          zip: yup
            .string()
            .matches(
              /^\d{5,}$/,
              "El código postal debe ser un número y tener al menos 5 dígitos"
            )
            .required("El código postal es requerido"),
          street: yup.string().required("La calle es requerida"),
          colony: yup.string().required("La colonia es requerida"),
          state: yup.string().required("El estado es requerido"),
          delegation: yup
            .string()
            .required("La delegación o municipio es requerido"),
          n_exterior: yup.string().required("El número exterior es requerido"),
          n_interior: yup.string().nullable(),
          address: yup.string().nullable(),
          phone: yup.string().nullable(),
          design: yup.string().required("El diseno es requerido"),
        });
      })
    )
    .min(1, "Debe tener al menos un consultorio")
    .required("Debe tener al menos un consuiltorio"),
});

export const confirmationSchema = yup.object().shape({
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
  gender: yup.string().ensure().nullable(), // Este campo no es requerido.
  fesa: yup.string().trim().required("El campo Código FESA es obligatorio"),
  password: yup.string().required("El campo Contraseña es obligatorio"),
  confirmPassword: yup
    .string()
    .required("Debe confirmar la contraseña")
    .oneOf([yup.ref("password")], "Las contraseñas deben coincidir"),

  specializations: yup
    .array()
    .of(SpecializationSchema)
    .min(1, "Debe tener al menos una especialización")
    .required("Debe tener al menos una especialización"),
  rooms: RoomArraySchema.fields.rooms,
});
