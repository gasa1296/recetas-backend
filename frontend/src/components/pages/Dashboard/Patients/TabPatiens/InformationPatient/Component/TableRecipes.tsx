import React from "react";
import { LuArrowRight } from "react-icons/lu";
import { HiPlusSmall } from "react-icons/hi2";
import { usePacients } from "@/store/pacients";

export default function TableRecipes({ nextStep }: any) {
  const { selectedPacient, SetStep, SetSelectedRecipe } = usePacients(
    (state) => ({
      selectedPacient: state.selectedPacient,
      SetSelectedRecipe: state.SetSelectedRecipe,
      SetStep: state.SetStep,
    })
  );

  const reversePreescriptions = [...selectedPacient?.prescriptions].reverse();

  return (
    <>
      <div className="flex flex-col md:flex-row justify-between mt-3 px-2">
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
        <div className="relative overflow-x-auto mt-5 ">
          <table className=" text-sm text-left  text-gray-500 dark:text-gray-400  w-full">
            <thead className=" text-[#4B4B4B]  text-[18px]  ">
              <tr>
                <th scope="col" className="px-6 py-3 ">
                  Folio
                </th>

                <th scope="col" className="px-6 py-3 ">
                  Diagnostico
                </th>
                <th scope="col" className="px-6 py-3 ">
                  Fecha
                </th>
                <th scope="col" className="px-6 py-3 ">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody className="text-[#1A1A1A] ">
              {reversePreescriptions.map((prescription: any, index: number) => {
                return (
                  <tr key={index} className="hover:bg-[#F7F8FA] text-[16px] ">
                    <td className="px-6 h-[52px] ">{prescription.code}</td>

                    <td className="px-6 h-[52px] ">
                      {prescription.diagnostic?.slice(0, 160)}
                      {prescription.diagnostic?.length > 160 ? "..." : ""}
                    </td>
                    <td className="px-6 h-[52px] ">
                      {new Date(prescription.created_at).toLocaleString()}
                    </td>
                    <td
                      className=" px-6 h-[52px] flex justify-start items-center text-[#FC6700] font-bold cursor-pointer  "
                      onClick={() => {
                        SetSelectedRecipe(prescription);
                        SetStep(4);
                      }}
                    >
                      <span className="pr-2">Ver recetas</span>
                      <LuArrowRight color="#FC6700" size={20} />
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
