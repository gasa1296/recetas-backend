import React from "react";
import TableRecipes from "./TableRecipes";
import FormGenerator from "@/components/FormGenerator";
import { usePacients } from "@/store/pacients";
import { Field } from "@/types/Generals/FormGenerator";
import { calculateAge } from "@/utils/getAge";
export default function SelectedUser({ nextStep }: any) {
    const { selectedPacient } = usePacients((state) => ({
        selectedPacient: state.selectedPacient,
    }));

    const submitData = async () => {
        //nextStep();
    };

    const fields: Field[] = [
        {
            label: "Paciente",
            name: "title",
            required: true,
            type: "subtitle",
        },
        {
            label: "Nombre(s)",
            name: "first_name",
            required: true,
            type: "text",
            width: 50,
            disabled: true,
            default: `${selectedPacient?.first_name} ${selectedPacient?.last_name1} ${selectedPacient?.last_name2}`,
        },
        {
            label: "Correo electrónico",
            name: "email",
            required: true,
            type: "email",
            width: 50,
            disabled: true,
            default: selectedPacient?.email,
        },
        {
            label: "Teléfono celular *",
            name: "phone1",
            required: true,
            type: "text",
            width: 50,
            disabled: true,
            default: selectedPacient?.phone1,
        },
        {
            label: "Edad *",
            name: "birth_date",
            required: true,
            type: "number",
            width: 50,
            disabled: true,
            default: calculateAge(selectedPacient?.birth_date),
        },
    ];
    return (
        <div>
            <div className="mt-3">
                <FormGenerator
                    submitData={submitData}
                    fields={fields}
                    loading={false}
                    renderButton={(handleSubmit) => (
                        <div className="flex justify-center w-full  "></div>
                    )}
                />
            </div>
            <TableRecipes nextStep={nextStep} />
        </div>
    );
}
