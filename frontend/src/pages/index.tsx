import React from "react";
import Image from "next/image";
import {
  Phone,
  Clock,
  ChevronRight,
  EyeOff,
  Eye,
  Loader2,
  UserCircle,
} from "lucide-react";

import Link from "next/link";
import AuthDesignLayout from "@/components/Layouts/AuthDesignLayout";
import { ILoginPayload } from "@/types/Store/Register";
import { useAuthStore } from "@/store/auth";
import { useMedicamentStore } from "@/store/medicaments";
import { useRecipeStore } from "@/store/recipes";
import { usePacients } from "@/store/pacients";
import { useRouter } from "next/router";
import { useForm } from "react-hook-form";
import { useState } from "react";
import Medicos from "@/assets/login/Medicos.png";
import { HelpAndHoursSection } from "@/components/register/HelpAndHoursSection";

export default function LoginPage() {
  const router = useRouter();

  const { Login, loading } = useAuthStore((state) => ({
    Login: state.Login,
    loading: state.loading,
  }));

  const ClearRecipe = useRecipeStore((state) => state.ClearRecipe);
  const ResetPacients = usePacients((state) => state.ResetPacients);
  const SetTabStep = usePacients((state) => state.SetTabStep);
  const { ResetMedicaments } = useMedicamentStore((state) => ({
    ResetMedicaments: state.ResetMedicaments,
  }));

  const submitData = async (data: ILoginPayload) => {
    const result = await Login(data);
    if (result) {
      if (result?.recetasUser === false) {
        return router.push("/custom-register");
      } else router.push("/dashboard");

      ResetPacients();
      ResetMedicaments();
      SetTabStep(0);
      ClearRecipe();
    }
  };
  const {
    handleSubmit,
    register,
    watch,
    formState: { errors },
  } = useForm({
    defaultValues: {
      email: "",
      password: "",
    },
  });

  const [showPassword, setShowPassword] = useState(false);
  const toggleShowPassword = (e: React.MouseEvent<HTMLButtonElement>) => {
    e.preventDefault();
    setShowPassword(!showPassword);
  };

  return (
    <AuthDesignLayout>
      {/* Main content */}
      <main className=" container mx-auto px-4 py-8  ">
        <div className="grid md:grid-cols-12 gap-6 max-w-[900px] mx-auto items-center justify-center">
          {/* Login section */}
          <div className="flex flex-col col-span-12 md:col-span-8 ">
            <p
              className="text-white text-lg mb-1 w-[240px] text-center rounded-full px-2 py-1"
              style={{
                background:
                  "linear-gradient(93.54deg, #FFFFFF -32.76%, #C9B28D 22.94%, #847FCB 63.53%, #423F65 132.76%)",
              }}
            >
              <strong>Inicia sesión</strong> como médico
            </p>
            <p className="text-black text-lg mb-1">
              <span className="text-[#27348B] font-bold">¡Ingresa</span> y
              gestiona todos tus servicios profesionales en un solo lugar.!
            </p>
            <div className="flex items-center  gap-4 mb-4">
              <div className="">
                <Image
                  src={Medicos}
                  alt="Doctor"
                  width={300}
                  height={300}
                  className=" h-16 w-16"
                />
              </div>
              <div>
                <h1 className="text-[40px] font-bold leading-none">
                  <span className="text-blue-500">
                    <span className="text-[#27348B]">¡Tu</span>{" "}
                    <span
                      style={{
                        background:
                          "linear-gradient(91.65deg, #FFFFFF -30.52%, #C9B28D 23.68%, #847FCB 63.16%, #423F65 130.52%)",
                        WebkitBackgroundClip: "text",
                        WebkitTextFillColor: "transparent",
                        backgroundClip: "text",
                      }}
                    >
                      práctica médica
                    </span>
                  </span>
                  <br />
                  <span className="text-[#27348B]">más eficiente!</span>
                </h1>
              </div>
            </div>
            <p className="text-[#27348B] font-medium text-md mt-1">
              Para profesionales de la salud
            </p>

            <form
              onSubmit={handleSubmit(submitData)}
              className="mt-4 space-y-4"
            >
              <div>
                <label
                  htmlFor="email"
                  className={`block text-sm font-medium ${
                    errors["email"] ? "text-red-500" : "text-[#003480]"
                  } mb-1`}
                >
                  Correo electrónico*
                </label>
                <input
                  id="email"
                  type="email"
                  {...register("email", { required: true })}
                  placeholder="Ingresa tu correo electrónico"
                  className={`${
                    errors["email"] && "border-red-400"
                  } w-full border border-gray-300 rounded-md p-2`}
                />
              </div>

              <div>
                <label
                  htmlFor="password"
                  className={`block text-sm font-medium ${
                    errors["password"] ? "text-red-500" : "text-[#003480]"
                  } mb-1`}
                >
                  Contraseña*
                </label>
                <div className="relative">
                  <input
                    id="password"
                    type={showPassword ? "text" : "password"}
                    {...register("password", { required: true })}
                    placeholder="Ingresa tu contraseña"
                    className={`${
                      errors["password"] && "border-red-400"
                    } w-full border border-gray-300 rounded-md pr-10 p-2`}
                  />
                  <button
                    onClick={toggleShowPassword}
                    className="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                  >
                    {showPassword ? (
                      <EyeOff className="h-4 w-4" />
                    ) : (
                      <Eye className="h-4 w-4" />
                    )}
                  </button>
                </div>
              </div>

              <button
                disabled={loading}
                className={`w-full flex items-center justify-center gap-2 h-12 rounded-3xl ${
                  !watch("email") || !watch("password")
                    ? "bg-gray-200 hover:bg-gray-300 text-gray-700"
                    : "bg-black hover:bg-black/80 text-white"
                }`}
              >
                {loading ? (
                  <Loader2 className="h-5 w-5 animate-spin" />
                ) : (
                  <>
                    Iniciar sesión como médico
                    <ChevronRight className="h-5 w-5" />
                  </>
                )}
              </button>

              <div className="flex justify-between text-sm ">
                <button
                  onClick={(e) => {
                    e.preventDefault();
                    router.push("/forgotPassword");
                  }}
                  className="text-black underline"
                >
                  Olvidé mi contraseña
                </button>
                <div className="text-black">
                  ¿Eres nuevo?{" "}
                  <Link href="/register" className="underline">
                    Crea tu cuenta aquí
                  </Link>
                  .
                </div>
              </div>
            </form>
          </div>

          {/* Right side with registration and info cards */}
          <div className="col-span-12 md:col-span-4 ">
            <HelpAndHoursSection />
          </div>
        </div>
      </main>

      {/* Banner before footer - now with border radius */}
      <div className="container mx-auto px-4 py-8 ">
        <div className="bg-white rounded-[40px] shadow-sm py-6 px-4 text-center">
          <h2 className="text-[28px] font-bold mb-1">
            <span
              className="bg-gradient-to-r from-[#003480] to-[#00ABE7] text-transparent bg-clip-text"
              style={{
                background:
                  "linear-gradient(91.65deg, #FFFFFF -30.52%, #C9B28D 23.68%, #847FCB 63.16%, #423F65 130.52%)",
                WebkitBackgroundClip: "text",
                WebkitTextFillColor: "transparent",
                backgroundClip: "text",
              }}
            >
              Conoce cómo mejorar tu práctica con nuestros beneficios
              exclusivos.
            </span>
          </h2>
          <p className="text-black text-sm">
            Consulta todos los servicios y herramientas que tenemos para ti.
            <Link
              href="https://www.farmaciasespecializadas.com/app-medicos"
              className="text-black font-semibold ml-1 underline"
            >
              Da clic aquí
            </Link>
            .
          </p>
        </div>
      </div>

      {/* WhatsApp floating button */}
      <div className="fixed bottom-6 right-6 z-50">
        <Link
          target="_blank"
          href="https://api.whatsapp.com/send?phone=525555883372&text=%C2%A1Hola!%20Quisiera%20m%C3%A1s%20informaci%C3%B3n%20de..."
          className="bg-green-500 text-white p-4 rounded-full shadow-lg flex items-center justify-center hover:bg-green-600"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            className="h-6 w-6"
            fill="currentColor"
            viewBox="0 0 24 24"
          >
            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
          </svg>
        </Link>
      </div>
    </AuthDesignLayout>
  );
}

/* import AuthLayout from "@/components/Layouts/AuthLayout";
import Home from "@/components/pages/home";
export default function HomePage() {
    return (
        <main>
            <AuthLayout>
                <Home />
            </AuthLayout>
        </main>
    );
}
 */
