import { useAuthStore } from "@/store/auth";
import { IRecoverPayload } from "@/types/Store/Register";
import { useRouter } from "next/router";
import React from "react";
import { useForm } from "react-hook-form";
import toast from "react-hot-toast";

export default function RecoverForm({ setSuccess }: { setSuccess: any }) {
    const router = useRouter();
    const { RecoverPassword, loading } = useAuthStore((state) => ({
        RecoverPassword: state.RecoverPassword,
        loading: state.loading,
    }));

    const submitData = async (data: IRecoverPayload) => {
        if (data.password !== data.password_confirmation) {
            return toast.error("Las contraseñas no coinciden");
        }

        const result = await RecoverPassword(data);
        console.log("first", result);
        if (result) router.push("/");
    };
    const {
        handleSubmit,
        register,
        formState: { errors },
    } = useForm({
        defaultValues: {
            password_confirmation: "",
            password: "",
        },
    });
    return (
        <form
            className=" pt-9 flex justify-center items-center flex-col"
            onSubmit={handleSubmit(submitData)}
        >
            <input
                type="Password"
                {...register("password", { required: true })}
                placeholder="Nuevo contraseña*"
                minLength={8}
                className={`${
                    errors["password"] && "border-red-400"
                } w-full max-w-[380px] p-1 border border-[ #DBE2EA] shadow-xl shadow-[#2C27380A] h-[52px] text-[#141414] focus:outline-none rounded-lg pl-2`}
            />
            <input
                type="Password"
                {...register("password_confirmation", { required: true })}
                placeholder="Confirmar contraseña*"
                minLength={8}
                className={`${
                    errors["password_confirmation"] && "border-red-400"
                } my-4 w-full  max-w-[380px] p-1 border border-[ #DBE2EA] shadow-xl shadow-[#2C27380A] h-[52px] text-[#141414] focus:outline-none rounded-lg pl-2`}
            />

            <button className="bg-[#000000] p-3 text-[#EBF4F8] rounded-lg w-full max-w-[220px] mx-auto block my-8">
                Cambiar contraseña
            </button>
        </form>
    );
}
