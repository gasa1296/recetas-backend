import React from "react";
import Image from "next/image";
import logo from "../../../assets/LogoFESA.svg";
export default function Footer() {
    return (
        <footer>
            <section className=" bg-[#E8E8E8] p-5  block md:flex justify-around">
                <div className=" justify-center  ">
                    <Image
                        src={logo}
                        alt="logo"
                        className="w-[194px] md:mx-0 mx-auto md:flex block "
                    />
                </div>

                <div className=" justify-center p-3  border md:text-start text-center ">
                    <ul>
                        <h3 className="font-bold text-[18px] mb-3 text-[#424242]">
                            Legales
                        </h3>

                        <li className="text-[#2D3540]">Aviso de Privacidad</li>
                        <li className="text-[#2D3540]">Preguntas frecuentes</li>
                    </ul>
                </div>

                <div className="p-3   text-center mx-auto md:mx-0 max-w-[350px]  w-full">
                    <h3 className="font-bold text-[18px]  mb-3 text-[#424242]">
                        ¿Necesitas ayuda?
                    </h3>
                    <p className="text-center block md:flex justify-center mb-3 text-[#2D3540] ">
                        Comunícate al
                        <p className="font-bold ms-1"> 55 5278 4540</p>
                    </p>
                    <p className="text-[#2D3540] ">
                        Horario de Centro de Atención a Clientes: Lunes a
                        Viernes: de 8:00am a 9:00pm. Sábados: de 8:00am a
                        7:00pm. Domingos: de 9:00am a 2:00pm
                    </p>
                </div>
            </section>
            <section className="text-center py-7 font-light">
                <p className="text-[#141414] text-[14px]">
                    © 2022 FÁRMACOS ESPECIALIZADOS. Todos los derechos
                    reservados
                </p>
            </section>
        </footer>
    );
}
