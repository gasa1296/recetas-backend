import { useMedicamentStore } from "@/store/medicaments";
import { usePacients } from "@/store/pacients";
import React from "react";
import { HiPlusSmall } from "react-icons/hi2";
export default function MedicineNotFound() {
  const { SetStep } = useMedicamentStore((state) => ({
    SetStep: state.SetStep,
  }));
  return (
    <section
      className="flex flex-col md:flex-row text-black justify-center  w-full p-20 "
      style={{
        background: "#F7F8FA 0% 0% no-repeat padding-box",
        border: "1px solid #DBE2EA",
      }}
    >
      <div className=" w-full  flex flex-col lg:flex-row justify-center items-center">
        <div className="text-left max-w-[400px]  mr-10 ">
          <span className="font-bold"> No se encontraron resultados</span> para
          tu búsqueda. Intenté con otra búsqueda o{" "}
          <span className="font-bold">agrega el medicamento.</span>
        </div>
        <div className=" mt-4 lg:mt-0 max-w-[300px]">
          <button
            onClick={() => SetStep(4)}
            className="flex justify-center items-center button-BlacK p-2 px-3"
          >
            <HiPlusSmall size={25} />
            Agregar nuevo
          </button>
        </div>
      </div>
    </section>
  );
}
