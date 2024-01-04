import React from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { usePacients } from "@/store/pacients";
import { IPacient } from "@/types/Models/Pacient";
export default function CreateUser() {
    const { CreatePacient } = usePacients((state) => ({
        CreatePacient: state.CreatePacient,
    }));
    const submitData = async (data: IPacient) => {
        CreatePacient(data);
    };

    const fields: Field[] = [
        {
            label: "Ingresa la siguiente información para dar de alta al paciente.",
            name: "title",
            required: true,
            type: "subtitle",
        },
        {
            label: "Nombre(s) *",
            name: "first_name",
            required: true,
            type: "text",
            width: 50,
        },
        {
            label: "Apellido Paterno *",
            name: "last_name1",
            required: true,
            type: "text",
            width: 50,
        },
        {
            label: "Apellido Materno *",
            name: "last_name2",
            required: true,
            type: "text",
            width: 50,
        },
        {
            label: "Correo electrónico *",
            name: "email",
            required: true,
            type: "email",
            width: 50,
        },
        {
            label: "Teléfono celular *",
            name: "phone1",
            required: true,
            type: "text",
            width: 50,
        },
        {
            label: "Teléfono fijo *",
            name: "phone2",
            required: true,
            type: "text",
            width: 50,
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
        },
        {
            label: "Fecha de nacimiento *",
            name: "birth_date",
            required: true,
            type: "date",
            width: 50,
        },
    ];
    return (
        <div className="mt-3">
            <FormGenerator
                submitData={submitData}
                fields={fields}
                loading={false}
                buttonText="Guardar"
            />
        </div>
    );
}
