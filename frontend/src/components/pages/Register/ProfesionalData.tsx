import FormGenerator from "@/components/FormGenerator";
import { useRegisterStore } from "@/store/register";
import { Field } from "@/types/Generals/FormGenerator";
import { IForm2 } from "@/types/Store/Register";
import { validateSameObject } from "@/utils/isSameObject";
import React from "react";

export default function ProfesionalData({ nextStep, backStep }: any) {
    const setForm2 = useRegisterStore((state) => state.setForm2);
    const form2 = useRegisterStore((state) => state.form2);
    const submitData = async () => {
        nextStep();
    };

    const fields: Field[] = [
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
    ];
    return (
        <section className="max-w-[1000px] mx-auto px-3 md:px-2">
            <h2 className="text-center text-[#1A1A1A] text-[28px] mt-5 font-medium mb-7">
                Ingrese su información profesional
            </h2>

            <FormGenerator
                submitData={submitData}
                fields={fields}
                loading={false}
                onFormChange={(form) => {
                    if (validateSameObject(form2 as object, form)) {
                        setForm2(form as IForm2);
                    }
                }}
                buttonText="Continuar"
                renderButton={(handleSubmit) => (
                    <div className="flex justify-center w-full ">
                        <button
                            onClick={(e) => {
                                e.preventDefault();
                                backStep();
                            }}
                            disabled={false}
                            className="bg-white font-bold border-black border-2 p-3 text-black rounded-lg w-60 mx-3 block my-4"
                            type="submit"
                        >
                            Anterior
                        </button>
                        <button
                            onClick={(e) => {
                                e.preventDefault();
                                handleSubmit();
                            }}
                            disabled={false}
                            className="bg-[#000000] p-3 text-[#EBF4F8] rounded-lg w-60 mx-3 block my-4"
                            type="submit"
                        >
                            Continuar
                        </button>
                    </div>
                )}
            />
        </section>
    );
}
