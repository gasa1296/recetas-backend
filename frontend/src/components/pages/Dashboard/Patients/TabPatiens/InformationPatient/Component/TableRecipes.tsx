import React from "react";
import { LuArrowRight } from "react-icons/lu";
import { HiPlusSmall } from "react-icons/hi2";
import { usePacients } from "@/store/pacients";

export default function TableRecipes({ nextStep }: any) {
    const { selectedPacient } = usePacients((state) => ({
        selectedPacient: state.selectedPacient,
    }));

    const recipes = [
        {
            Folio: "1289",
            NombrePaciente: "Juan Perez",
            CorreoElectrónico: "victor.hernandez@fanafesa.com",
            Fecha: "12/12/2021",
            Acciones: "Ver receta",
        },
        {
            Folio: "1645",
            NombrePaciente: "Juan Perez",
            CorreoElectrónico: "victor.hernandez@fanafesa.com",
            Fecha: "24/03/2023",
            Acciones: "Ver receta",
        },
        {
            Folio: "1879",
            NombrePaciente: "Juan Perez",
            CorreoElectrónico: "victor.hernandez@fanafesa.com",
            Fecha: "12/05/2023",
            Acciones: "Ver receta",
        },
    ];

    return (
        <>
            <div className="flex justify-between mt-3 px-2">
                <p className="title-RecetasGeneradas">
                    Recetas generadas a su paciente
                </p>
                <button
                    onClick={() => nextStep()}
                    className="flex justify-center items-center button-BlacK p-2 px-3"
                >
                    <HiPlusSmall size={25} />
                    Nueva receta
                </button>
            </div>

            {selectedPacient?.prescriptions.length ? (
                <div className="relative overflow-x-auto mt-5">
                    <table className="w-full text-sm text-left  text-gray-500 dark:text-gray-400">
                        <thead className="text-xs text-[#4B4B4B] uppercase ">
                            <tr>
                                <th scope="col" className="px-6 py-3">
                                    Folio
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Nombre paciente
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Correo electrónico
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Fecha
                                </th>
                                <th scope="col" className="px-6 py-3">
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody className="text-[#1A1A1A]">
                            {recipes.map((patient, index) => {
                                return (
                                    <tr className="hover:bg-[#F7F8FA]">
                                        <td className="px-6 h-[52px] ">
                                            {patient.Folio}
                                        </td>
                                        <td className="px-6 h-[52px] ">
                                            {patient.NombrePaciente}
                                        </td>
                                        <td className="px-6 h-[52px] ">
                                            {patient.CorreoElectrónico}
                                        </td>
                                        <td className="px-6 h-[52px] ">
                                            {patient.Fecha}
                                        </td>
                                        <td className=" px-6 h-[52px] flex justify-start items-center text-[#FC6700] font-bold cursor-pointer">
                                            {patient.Acciones}
                                            <LuArrowRight color="#FC6700" />{" "}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            ) : null}
        </>
    );
}
