import { useAuthStore } from "@/store/auth";
import { ILoginPayload } from "@/types/Store/Register";
import { useRouter } from "next/router";
import React, { useState } from "react";
import { useForm } from "react-hook-form";
import { FaEye, FaEyeSlash } from "react-icons/fa";
export default function LoginForm() {
  const router = useRouter();
  const { Login, loading } = useAuthStore((state) => ({
    Login: state.Login,
    loading: state.loading,
  }));

  const submitData = async (data: ILoginPayload) => {
    const result = await Login(data);
    if (result) router.push("/dashboard");
  };
  const {
    handleSubmit,
    register,
    formState: { errors },
  } = useForm({
    defaultValues: {
      email: "",
      password: "",
    },
  });

  const [showPassword, setShowPassword] = useState(false);
  const toggleShowPassword = () => setShowPassword(!showPassword);

  return (
    <form onSubmit={handleSubmit(submitData)}>
      <input
        type="email"
        {...register("email", { required: true })}
        placeholder="Correo electrónico"
        className={`${
          errors["email"] && "border-red-400"
        } my-4 w-full p-1 border border-[ #DBE2EA] shadow-xl shadow-[#2C27380A] h-[52px] text-[#141414] focus:outline-none rounded-lg pl-2`}
      />
      <div className="relative">
        <input
          type={showPassword ? "text" : "password"}
          {...register("password", { required: true })}
          placeholder="Password"
          className={`${
            errors["password"] && "border-red-400"
          } w-full p-1 border border-[ #DBE2EA] shadow-xl shadow-[#2C27380A] h-[52px] text-[#141414] focus:outline-none rounded-lg pl-2`}
        />

        <div
          className="absolute z-[1000] inset-y-0 right-0 flex items-center pr-3 cursor-pointer"
          onClick={toggleShowPassword}
        >
          {showPassword ? <FaEyeSlash /> : <FaEye />}
        </div>
      </div>
      <button
        disabled={loading}
        className="border disabled:opacity-40 bg-[#000000] text-[#EBF4F8] w-full p-2  my-4 shadow-md rounded-lg"
      >
        Iniciar sesión
      </button>
    </form>
  );
}
