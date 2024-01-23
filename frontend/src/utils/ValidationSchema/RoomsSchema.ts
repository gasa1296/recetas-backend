import * as yup from "yup";

export const RoomSchema = yup.object().shape({
  name: yup.string().required("El nombre del consultorio es requerido"),
  zip: yup.string().required("El código postal es requerido"),
  street: yup.string().required("La calle es requerida"),
  colony: yup.string().required("La colonia es requerida"),
  state: yup.string().required("El estado es requerido"),
  delegation: yup.string().required("La delegación o municipio es requerido"),
  n_exterior: yup.string().required("El número exterior es requerido"),
  n_interior: yup.string().nullable(),
  address: yup.string().nullable(),
  phone: yup
    .string()

    .nullable(),

  design: yup.string().required("El diseno es requerido"),
  files: yup.array().min(1).required("Los archivos son requeridos"),
});

export const RoomProfileSchema = yup.object().shape({
  name: yup.string().required("El nombre del consultorio es requerido"),
  zip: yup.string().required("El código postal es requerido"),
  street: yup.string().required("La calle es requerida"),
  colony: yup.string().required("La colonia es requerida"),
  state: yup.string().required("El estado es requerido"),
  delegation: yup.string().required("La delegación o municipio es requerido"),
  n_exterior: yup.string().required("El número exterior es requerido"),
  n_interior: yup
    .string()

    .nullable(),
  address: yup.string().nullable(),
  phone: yup
    .string()

    .nullable(),

  design: yup.string().required("El diseno es requerido"),
  logo: yup.array().min(1).required("Los archivos son requeridos"),
});
