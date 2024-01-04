import * as yup from "yup";
export const SpecializationSchema = yup.object().shape({
    name: yup.string().required("El nombre es requerido"),
    identification: yup.string().required("La identificación es requerida"),
    university: yup.string().required("La universidad es requerida"),
    file: yup.array().min(1).required("Los archivos son requeridos"),
});

export const SpecializationProfileSchema = yup.object().shape({
    name: yup.string().required("El nombre es requerido"),
    identification: yup.string().required("La identificación es requerida"),
    university: yup.string().required("La universidad es requerida"),
    logo: yup.array().min(1).required("Los archivos son requeridos"),
});
