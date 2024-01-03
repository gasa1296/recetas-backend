import { useRouter } from "next/router";
import React, { useState } from "react";
import { FaEnvelopeOpenText } from "react-icons/fa";
import RecoverForm from "./RecoverForm";
export default function RecoverPassword() {
    const [success, setSuccess] = useState(false);
    const router = useRouter();

    return (
        <section className="bg-[#F7F7F7] md:p-10 flex justify-center ">
            {!success ? (
                <div className="w-full max-w-[710px]  p-9 m-9 bg-[#FFFFFF]  shadow-lg shadow-[#2C27380A] ">
                    <h6 className="font-normal  text-[#333333] text-[28px] text-center">
                        Restablecer contraseña
                    </h6>
                    <RecoverForm setSuccess={setSuccess} />
                </div>
            ) : (
                <div className="w-full max-w-[710px]  p-9 m-9 bg-[#FFFFFF]  shadow-lg shadow-[#2C27380A] ">
                    <FaEnvelopeOpenText
                        size={60}
                        color={"#222020"}
                        className="mx-auto"
                    />
                    <h6 className="font-normal  text-[#333333] text-[24px] text-center">
                        Restablecimiento de contraseña exitoso
                    </h6>
                    <p className="text-[16px] mt-4 text-center text-[#818A91] ">
                        Ha restablecido su contraseña de forma exitosa, ya puede
                        iniciar sesión con su nueva contraseña.
                    </p>

                    <button
                        className="bg-black text-[#EBF4F8] max-w-[166px] w-full p-3 my-4 mt-8 rounded-lg block mx-auto"
                        onClick={() => router.push(`/`)}
                    >
                        Iniciar sesión
                    </button>
                </div>
            )}
        </section>
    );
}
