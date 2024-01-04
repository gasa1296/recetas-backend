import { usePacients } from "@/store/pacients";
import React from "react";
import { HiPlusSmall } from "react-icons/hi2";
export default function PacientsNotFound() {
    const { SetStep } = usePacients((state) => ({
        SetStep: state.SetStep,
    }));
    return (
        <section className=" flex text-black ">
            <div className="text-left">
                <span className="font-bold"> No se encontraron resultados</span>{" "}
                para tu búsqueda. Intenté con otra búsqueda o{" "}
                <span className="font-bold">crear un nuevo paciente </span>{" "}
                agregando su información.
            </div>
            <div className="w-[40%] flex items-center  justify-end">
                <button
                    onClick={() => SetStep(3)}
                    className="flex justify-center items-center button-BlacK p-2 px-3"
                >
                    <HiPlusSmall size={25} />
                    Agregar nuevo
                </button>
            </div>
        </section>
    );
}
