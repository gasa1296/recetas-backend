import { useRouter } from "next/router";
import React from "react";
import LoginForm from "./LoginForm";

export default function Home() {
    const router = useRouter();
    return (
        <section className=" md:p-10 bg-[#F7F7F7]  flex  flex-col lg:flex-row justify-center items-center  py-5 px-3">
            <div className=" w-full max-w-[400px]  h-full  p-7 m-0 lg:mx-10 bg-[#FFFFFF] shadow-lg shadow-[#2C27380A]  rounded-lg ">
                <h6 className="font-medium  text-[#141414] text-[28px]">
                    Registro
                </h6>
                <p className="text-[18px] mt-5 font-normal h-[118px]">
                    Si ya eres cliente FESA y no tiene usuario regístrese para
                    poder generar sus
                    <p className="text-[#FC6700]">
                        recetas médicas electrónicas
                    </p>
                    en nuestra plataforma
                </p>
                <button
                    className=" bg-black text-[#EBF4F8] w-full p-2 my-4 shadow-md rounded-lg "
                    onClick={() => router.push(`/register`)}
                >
                    Registro
                </button>
                <p className="text-[#FC6700] text-[16px] cursor-pointer text-center font-light pt-2 ">
                    ¿Quieres ser cliente FESA? Contáctanos
                </p>
            </div>

            <div className=" w-full max-w-[400px] h-full  p-7 m-0 lg:mx-10 bg-[#FFFFFF] mt-4 lg:mt-0 shadow-lg shadow-[#2C27380A]  rounded-lg">
                <h6 className="font-medium  text-[ #4B4B4B] text-[28px]">
                    Iniciar sesión
                </h6>
                <LoginForm />

                <p
                    className="text-[#FC6700] text-[16px] cursor-pointer text-center font-light pt-2"
                    onClick={() => router.push(`/forgotPassword`)}
                >
                    ¿Olvidaste tu contraseña?
                </p>
            </div>
        </section>
    );
}
