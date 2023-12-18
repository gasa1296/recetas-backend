import React from 'react'
import FormGenerator from '@/components/FormGenerator';
import { Field } from '@/types/Generals/FormGenerator';
export default function RegisterQuery({ nextStep, backStep }: any) {
    const submitData = async () => {

        nextStep();

    };
    const fields: Field[] = [
        {
            label: "Paciente",
            name: "confirmarContrasena",
            required: true,
            type: "title",
        },
        {
            label: "Nombre(s) *",
            name: "name",
            required: true,
            type: "text",
            width: 50,
        },
        {
            label: "Correo electrónico",
            name: "email",
            required: true,
            type: "text",
            width: 50,
        },
        { label: "Temperatura (°C)", name: "temperature", required: true, type: "text", width: 30, },
        { label: "Peso (kg)", name: "weight", required: true, type: "text", width: 30, },
        { label: "Talla (cm)", name: "size", required: true, type: "text", width: 30, },
        { label: "Presión arterial", name: "Blood-pressure", required: true, type: "text", width: 30, },
        { label: "Saturación (%)", name: "Saturation ", required: true, type: "text", width: 30, },
        { label: "Frecuencia cardiaca (ppm)", name: "Heart-rate", required: true, type: "text", width: 30, },
        {
            label: "Ingresa la siguiente información para dar de alta al paciente.",
            name: "confirmarContrasena",
            required: true,
            type: "title",
        },
        {
            label: "Diagnóstico Médico: *",
            name: "Medical-diagnostic",
            required: true,
            type: "textarea",
            width: 100,
        }, {
            label: "Alergias:",
            name: "Allergies",
            required: true,
            type: "textarea",
            width: 100,
        },
        {
            label: "Dietas especiales:",
            name: "Special-diets",
            required: true,
            type: "textarea",
            width: 100,
        }, {
            label: "Indicaciones médicas adicionales:",
            name: "Medical-indications",
            required: true,
            type: "textarea",
            width: 100,
        },


    ];
    return (
        <section className="px-12 p-3 bg-[#fff] mt-5">
            <FormGenerator
                submitData={submitData}
                fields={fields}
                loading={false}
                buttonText='Continuar'
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
                            Regresar
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
    )
}
