import { usePacients } from "@/store/pacients";
import React from "react";
import { FaUser } from "react-icons/fa";
export default function FindPacient() {
    const { SetStep } = usePacients((state) => ({
        SetStep: state.SetStep,
    }));
    return (
        <div className="mt-8 flex flex-col lg:flex-row justify-center items-center  container-box  cursor-pointer mx-auto">
            <FaUser size={60} color="#000" />
            <div className="">
                <p className=" mt-5 text-center  lg:mt-0 text-[16px] md:text-[28px] font-normal text-[#1A1A1A]  lg:ms-4 md:w-[380px]  ">
                    Para iniciar busque su paciente O dar de alta un{" "}
                    <span
                        onClick={() => SetStep(3)}
                        className=" text-[#FC6700] border-r-0 border-b-2 border-b-[#FC6700]"
                    >
                        nuevo paciente
                    </span>
                </p>
            </div>
        </div>
    );
}
