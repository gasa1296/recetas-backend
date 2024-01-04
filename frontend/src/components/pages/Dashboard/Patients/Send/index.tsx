import React from "react";
import { FaRegEnvelope, FaWhatsapp, FaChevronDown } from "react-icons/fa";
import { MdOutlineLocalPrintshop } from "react-icons/md";
export default function Send({ nextStep, backStep }: any) {
    return (
        <section className=" ">
            <div className="flex items-center justify-between border-Tab p-2 ps-3 mt-4">
                <div className="flex items-center ">
                    <FaRegEnvelope color="#Fff " size={28} />
                    <p className="text-[#fff] text-[26px] ms-3">Enviar</p>
                </div>

                <FaChevronDown size={28} color="#Fff " />
            </div>
            <section className="container-Patiens px-3 md:px-8 py-10 text-center">
                <p className="text-[#1A1A1A] text-[24px] font-bold">
                    Receta generada de forma exitosa
                </p>
                <p className="mt-8 text-[20px] text-[ #1A1A1A]">
                    Su receta fue generada de forma exitosa, puede compartirla
                    con su paciente mediante alguno de los siguientes medios:
                </p>
                <div className=" flex flex-wrap justify-center items-center mt-10 pb-10">
                    <button className="flex  justify-center items-center border button-print  mw-[15%] text-[20px] mt-4 p-1 px-10">
                        <MdOutlineLocalPrintshop size={18} />
                        <p className="mx-2 "> Imprimir</p>
                    </button>
                    <button className="flex items-center border justify-center button-BlacK  mw-[15%] text-[20px] mt-4 p-1 mx-3 px-10">
                        <FaWhatsapp color="#25d366" size={20} className="" />
                        <p className="mx-2 "> Enviar</p>
                    </button>
                    <button className="flex items-center justify-center button-white mw-[20%] p-1 px-10 mt-4">
                        <FaRegEnvelope color="#1A1A1A " size={18} />
                        <p className="mx-2 "> Enviar por correo</p>
                    </button>
                </div>
            </section>
        </section>
    );
}
