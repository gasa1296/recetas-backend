import FormGenerator from "@/components/FormGenerator";
import { useRegisterStore } from "@/store/register";
import { Field } from "@/types/Generals/FormGenerator";
import React from "react";
import * as yup from "yup";
import Receta1 from "@/assets/images/recetas/Receta1.png";
import Receta2 from "@/assets/images/recetas/Receta2.png";
import Receta3 from "@/assets/images/recetas/Receta3.png";
import { IForm3, IRegisterPayload } from "@/types/Store/Register";
import { SpecializationSchema } from "@/utils/ValidationSchema/SpecializationSchema";
import { RoomSchema } from "@/utils/ValidationSchema/RoomsSchema";

export default function ConfirmAccount({ nextStep, backStep }: any) {
    const { form3, form2, form1, handleSubmit, loading } = useRegisterStore(
        (state) => ({
            form3: state.form3,
            form2: state.form2,
            form1: state.form1,
            handleSubmit: state.handleSubmit,
            loading: state.loading,
        })
    );

    const submitData = async (data: IRegisterPayload) => {
        const result = await handleSubmit(data);

        if (result) nextStep();
    };

    const schema = yup.object().shape({
        specializations: yup
            .array()
            .of(SpecializationSchema)
            .min(1, "Debe tener al menos una especialización")
            .required("Debe tener al menos una especialización"),
        rooms: yup
            .array()
            .of(RoomSchema)
            .min(1, "Debe tener al menos un consultorio")
            .required("Debe tener al menos un consuiltorio"),
    });

    const fields: Field[] = [
        {
            label: "Nombre(s) *",
            name: "first_name",
            required: true,
            type: "text",
            width: 50,
            default: form1?.first_name || "",
        },
        {
            label: "Apellido Paterno *",
            name: "last_name1",
            required: true,
            type: "text",
            width: 50,
            default: form1?.last_name1 || "",
        },
        {
            label: "Apellido Materno *",
            name: "last_name2",
            required: true,
            type: "text",
            width: 50,
            default: form1?.last_name2 || "",
        },

        {
            label: "Correo electrónico *",
            name: "email",
            required: true,
            type: "email",
            width: 50,
            default: form1?.email || "",
        },

        {
            label: "Teléfono celular *",
            name: "phone1",
            required: true,
            type: "text",
            width: 50,
            default: form1?.phone1 || "",
        },
        {
            label: "Teléfono fijo *",
            name: "phone2",
            required: true,
            type: "text",
            width: 50,
            default: form1?.phone2 || "",
        },
        {
            label: "Seleccionar Género *",
            name: "gender",
            required: true,
            type: "select",
            options: [
                { label: "Masculino", value: "0" },
                { label: "Femenino", value: "1" },
            ],
            width: 50,
            default: form1?.gender || "",
        },
        {
            label: "Código FESA *",
            name: "fesa",
            required: true,
            type: "number",
            width: 50,
            default: form1?.fesa || "",
        },
        {
            label: "Contrasena *",
            name: "password",
            required: true,
            type: "password",
            width: 50,
            default: form1?.password || "",
        },
        {
            label: "Confirmar contrasena *",
            name: "confirmPassword",
            required: true,
            type: "password",
            width: 50,
            default: form1?.confirmPassword || "",
        },

        {
            label: "Datos profesionales",
            name: "title",
            required: true,
            type: "title",
            width: 100,
        },
        {
            label: "",
            name: "specializations",
            type: "subForm",
            buttonAddText: "Agregar especialidad",
            maxFile: 1,
            width: 100,
            default: form2?.specializations || [
                {
                    name: "",
                    identification: "",
                    university: "",
                    file: null,
                },
            ],
            form: [
                {
                    label: "Datos especialidad",
                    name: "title",
                    required: true,
                    type: "title",
                    width: 100,
                },
                {
                    label: "Licenciatura *",
                    name: "name",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "name",
                    default: form2?.specializations || "",
                },
                {
                    label: "Cédula profesional *",
                    name: "identification",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "identification",
                    default: form2?.specializations || "",
                },
                {
                    label: "Institución que otorga licenciatura *",
                    name: "university",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "university",
                    default: form2?.specializations || "",
                },
                {
                    label: "Agrega el logotipo de tu Universidad *",
                    name: "file",
                    maxFile: 1,
                    required: true,
                    type: "file",
                    width: 100,
                    subFormKey: "file",
                    default: form2?.specializations || "",
                },
            ],
        },

        {
            label: "Dirección del consultorio principal",
            name: "title",
            required: true,
            type: "title",
            width: 100,
        },

        {
            label: "",
            name: "rooms",
            type: "subForm",
            buttonAddText: "Agregar otro consultorio",
            maxFile: 1,
            width: 100,
            default: form3?.rooms || [
                {
                    name: "",
                    zip: "",
                    street: "",
                    colony: "",
                    state: "",
                    delegation: "",
                    n_exterior: "",
                    n_interior: "",
                    address: "",
                    phone: "",
                    file: null,
                    design: null,
                },
            ],
            form: [
                {
                    label: "Dirección del consultorio",
                    name: "title",
                    required: true,
                    type: "title",
                    width: 100,
                },
                {
                    label: "Nombre del consultorio *",
                    name: "name",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "name",
                    default: form3?.rooms || "",
                },
                {
                    label: "Codigo Postal *",
                    name: "zip",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "zip",
                    default: form3?.rooms || "",
                },
                {
                    label: "Calle *",
                    name: "street",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "street",
                    default: form3?.rooms || "",
                },
                {
                    label: "Colonia *",
                    name: "colony",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "colony",
                    default: form3?.rooms || "",
                },
                {
                    label: "Estado *",
                    name: "state",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "state",
                    default: form3?.rooms || "",
                },
                {
                    label: "Delegación o Municipio *",
                    name: "delegation",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "delegation",
                    default: form3?.rooms || "",
                },
                {
                    label: "Número exterior *",
                    name: "n_exterior",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "n_exterior",
                    default: form3?.rooms || "",
                },
                {
                    label: "Número interior (Optional)",
                    name: "n_interior",
                    required: false,
                    type: "text",
                    width: 50,
                    subFormKey: "n_interior",
                    default: form3?.rooms || "",
                },
                {
                    label: "Piso / Nº de local / No. Consultorio",
                    name: "address",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "address",
                    default: form3?.rooms || "",
                },
                {
                    label: "Teléfono de consultorio (Opcional)",
                    name: "phone",
                    required: false,
                    type: "text",
                    width: 50,
                    subFormKey: "phone",
                    default: form3?.rooms || "",
                },

                {
                    label: "Agrega el logotipo de su consultorio",
                    name: "files",
                    required: true,
                    type: "file",
                    width: 100,
                    subFormKey: "files",
                    default: form3?.rooms || "",
                    maxFile: 1,
                },

                {
                    label: "Diseño de la Receta",
                    name: "title3",
                    required: true,
                    type: "title",
                    width: 100,
                },

                {
                    label: "Elija el diseño para su receta de las siguientes opciones:",
                    name: "design",
                    required: false,
                    type: "recetas",
                    width: 100,
                    subFormKey: "design",
                    default: form3?.rooms || "",
                    recetasOptions: [
                        { image: Receta1, value: "1" },
                        { image: Receta2, value: "2" },
                        { image: Receta3, value: "3" },
                    ],
                },
            ],
        },
    ];
    return (
        <section className="max-w-[1000px] mx-auto px-3 md:px-2">
            <h2 className="text-center text-[#1A1A1A] text-[28px] mt-5 font-medium">
                Confirme la información de su cuenta
            </h2>
            <h4 className="font-bold text-[#4B4B4B] text-[24px] text-start my-7">
                Datos personales
            </h4>
            <div className="flex   ">
                <div className=" w-full">
                    <FormGenerator
                        buttonText="Continuar"
                        submitData={submitData}
                        fields={fields}
                        loading={false}
                        schema={schema}
                        renderButton={(handleSubmit) => (
                            <div className="flex justify-center w-full ">
                                <button
                                    onClick={(e) => {
                                        e.preventDefault();
                                        backStep();
                                    }}
                                    disabled={loading}
                                    className="bg-white disabled:opacity-40 font-bold border-black border-2 p-3 text-black rounded-lg w-60 mx-3 block my-4"
                                    type="submit"
                                >
                                    Anterior
                                </button>
                                <button
                                    onClick={(e) => {
                                        e.preventDefault();
                                        handleSubmit();
                                    }}
                                    disabled={loading}
                                    className="bg-[#000000] p-3 disabled:opacity-40 text-[#EBF4F8] rounded-lg w-60 mx-3 block my-4"
                                    type="submit"
                                >
                                    Registrarse
                                </button>
                            </div>
                        )}
                    />
                </div>
            </div>
        </section>
    );
}
