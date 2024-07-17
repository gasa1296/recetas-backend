import { useMedicamentStore } from "@/store/medicaments";
import React from "react";
import { FaRegUser, FaUser, FaPills } from "react-icons/fa";
import { PopularMedicaments } from "../../PopularMedicaments";
import useScrollToTop from "@/hooks/useScrollToTop";

export default function FindMedicine() {
  const { SetStep } = useMedicamentStore((state) => ({
    SetStep: state.SetStep,
  }));
  useScrollToTop();
  return (
    <>
      {/* <div className="mt-4 flex flex-col lg:flex-row justify-center items-center  container-box container-box2 mx-auto cursor-pointer  p-0">
        <FaPills size={40} color="#000" />
        <div className="">
          <p className=" pl-4 text-[16px] md:text-[20px] font-normal text-[#1A1A1A]  ms-4 md:w-full  max-w-[530px]">
            Antes de generar la receta, busque los medicamentos que requiera su
            paciente o agrega{" "}
            <span
              onClick={() => SetStep(4)}
              className=" text-[#FC6700] border-r-0 border-b-2 border-b-[#FC6700]"
            >
              otro medicamento.
            </span>
          </p>
        </div>
      </div> */}
      <PopularMedicaments />
    </>
  );
}
