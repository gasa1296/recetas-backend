import { useRouter } from "next/router";
import React from "react";
import { FaEnvelopeOpenText } from "react-icons/fa";
export default function RegisterSucces() {
    const router = useRouter();

    return (
        <section className="bg-[#F7F7F7] md:p-10 flex justify-center">
            <div className="w-full max-w-[710px]  p-9 m-9 bg-[#FFFFFF]  shadow-lg shadow-[#2C27380A] ">
                <FaEnvelopeOpenText
                    size={70}
                    color={"#222020"}
                    className="mx-auto"
                />
                <h6 className="font-normal  text-[#333333] pt-4 text-[24px] text-center">
                    ¡Gracias por registrarte!
                </h6>
                <p className="text-[16px] my-8 mt-4 text-center text-[#818A91] w-full max-w-[600px]">
                    Solo falta un paso, debes verificar tu dirección de correo
                    electrónico. Te enviamos el link de verificación al correo
                    electrónico que registraste.
                </p>

                <button
                    className="bg-black text-[#EBF4F8] max-w-[166px] w-full p-3 my-4 rounded-lg block mx-auto"
                    onClick={() => router.push(`/`)}
                >
                    Volver al inicio
                </button>
            </div>
        </section>
    );
}
