import React from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { FaTrash, FaPlus } from "react-icons/fa";
import { useMedicamentStore } from "@/store/medicaments";
import { IMedicament } from "@/types/Models/Medicament";

export default function AddNewMedicine({ nextStep, backStep }: any) {
    const { CreateMedicament, SetStep } = useMedicamentStore((state) => ({
        CreateMedicament: state.CreateMedicament,
        SetStep: state.SetStep,
    }));

    function generateUUID() {
        return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(
            /[xy]/g,
            function (c) {
                var r = (Math.random() * 16) | 0,
                    v = c === "x" ? r : (r & 0x3) | 0x8;
                return v.toString(16);
            }
        );
    }

    const submitData = async (data: IMedicament) => {
        CreateMedicament({ ...data, uicodproducto: generateUUID(), new: true });
    };
    const fields: Field[] = [
        {
            label: "Nombre del medicamento *",
            name: "name",
            required: true,
            type: "text",
        },
        {
            label: "Dosis *",
            name: "dose",
            required: true,
            type: "text",
            width: 50,
            default: "",
        },
        {
            label: "Frecuencia *",
            name: "frequency",
            required: true,
            type: "text",
            width: 50,
            default: "",
        },
        {
            label: "Duración *",
            name: "duration",
            required: true,
            type: "text",
            width: 50,
            default: "",
        },
        {
            label: "Via de administración *",
            name: "via",
            maxFile: 1,
            required: true,
            type: "text",
            width: 50,
            default: "",
        },
        {
            label: "Indicaciones adicionales *",
            name: "indications",
            maxFile: 1,
            required: true,
            type: "text",
            width: 50,
            default: "",
        },
        {
            label: "Cantidad total de medicamento por tratamiento *",
            name: "quantity",
            maxFile: 1,
            required: true,
            type: "text",
            width: 50,
            default: "",
        },
    ];
    return (
        <section>
            <p className="text-[#4B4B4B] text-[16px] font-bold mt-3">
                Resultados de su búsqueda
            </p>
            <div className="container-AddMdicine px-10 py-3 mt-5 mb-6">
                <div className="flex justify-between">
                    <p className="text-[#4B4B4B] text-[18px] font-bold mt-3">
                        Llene los datos para su medicamento
                    </p>
                    <button
                        onClick={() => SetStep(1)}
                        className="button-BlacK flex justify-center items-center p-2 w-[120px] "
                    >
                        <FaTrash size={20} /> <p className="ms-1"> Eliminar</p>{" "}
                    </button>
                </div>
                <div className="mt-6 pb-3">
                    <FormGenerator
                        submitData={submitData}
                        fields={fields}
                        loading={false}
                        renderButton={(handleSubmit) => (
                            <div className="flex justify-center w-full  ">
                                <button
                                    className="button-BlacK disabled:opacity-40 md:flex justify-center items-center border-black border-2 p-1 text-black rounded-lg w-full mx-3 block my-4"
                                    type="submit"
                                >
                                    <FaPlus
                                        color="#fbfbfb"
                                        className="me-3"
                                        size={20}
                                    />
                                    Agregar medicamento a la receta
                                </button>
                            </div>
                        )}
                    />
                </div>
            </div>
        </section>
    );
}
