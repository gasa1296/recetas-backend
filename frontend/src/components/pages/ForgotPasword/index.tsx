import { useAuthStore } from "@/store/auth";
import { IForgotPayload } from "@/types/Store/Register";
import { useRouter } from "next/router";
import React, { useState } from "react";
import { useForm } from "react-hook-form";
export default function ForgortPassword({ nextStep }: any) {
    const [success, setSuccess] = useState(true);
    const router = useRouter();

    const { ForgotPassword, loading } = useAuthStore((state) => ({
        ForgotPassword: state.ForgotPassword,
        loading: state.loading,
    }));

    const submitData = async (data: IForgotPayload) => {
        const result = await ForgotPassword(data);
        if (result) setSuccess(false);
    };

    const {
        handleSubmit,
        register,
        formState: { errors },
    } = useForm({
        defaultValues: {
            email: "",
        },
    });
    return (
        <section className="bg-[#F7F7F7] md:p-10 flex justify-center ">
            {success ? (
                <div className="w-full max-w-[710px]  p-9 m-9 bg-[#FFFFFF]  shadow-lg shadow-[#2C27380A] ">
                    <h6 className="font-normal  text-[#333333] text-[28px] text-center">
                        Restablecer contraseña
                    </h6>
                    <p className="text-[18px] mt-4 text-center text-[#818A91] ">
                        Para restablecer su contraseña por favor ingrese la
                        dirección de correo electrónico que utilizó para crear
                        su cuenta.
                    </p>
                    <p className="text-[#818A91] text-[18px] text-center  mb-5">
                        Le enviaremos un enlace de restablecimiento de
                        contraseña a esa dirección.
                    </p>
                    <form
                        className=" flex flex-col justify-center items-center"
                        onSubmit={handleSubmit(submitData)}
                    >
                        <input
                            type="email"
                            {...register("email", { required: true })}
                            placeholder="Correo electrónico"
                            className={`${
                                errors["email"] && "border-red-400"
                            } my-4 w-full p-1 border border-[ #DBE2EA] shadow-xl shadow-[#2C27380A] h-[52px] text-[#141414] max-w-[350px] focus:outline-none rounded-lg pl-2`}
                        />

                        <button
                            disabled={loading}
                            className="bg-[#000000]  disabled:opacity-40 p-3 text-[#EBF4F8] rounded-lg w-full max-w-[220px] mx-auto block my-8"
                        >
                            Restablecer contraseña
                        </button>
                    </form>
                </div>
            ) : (
                <div className="w-full max-w-[710px]  p-9 m-9 bg-[#FFFFFF]  shadow-lg shadow-[#2C27380A] ">
                    <h6 className="font-normal  text-[#333333] text-[28px] text-center">
                        Restablecer contraseña
                    </h6>
                    <p className="text-[18px] py-8 text-center text-[#818A91] ">
                        Si existe una cuenta que coincida con los detalles
                        proporcionados, le enviaremos un enlace para restablecer
                        la contraseña. Por favor revisa tu bandeja de entrada o
                        correos no deseados
                    </p>

                    <button
                        className="bg-black text-[#EBF4F8] max-w-[166px] w-full p-3 my-4 rounded-lg block mx-auto"
                        onClick={() => router.push(`/recoverPassword`)}
                    >
                        Volver al inicio
                    </button>
                </div>
            )}
        </section>
    );
}
