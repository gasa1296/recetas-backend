"use client";
import React, { useState } from "react";
import { ChevronRight, AlertTriangle, Loader2 } from "lucide-react";
import { useForm } from "react-hook-form";
import { activateProfile } from "@/services/auth";
import { IActivatePayload } from "@/types/Store/Register";
import { toast } from "react-hot-toast";
import Link from "next/link";
import { useRouter } from "next/navigation";
export default function RegisterForm({
  setSuccessScreen,
}: {
  setSuccessScreen: (success: boolean) => void;
}) {
  const [loading, setLoading] = useState(false);
  const router = useRouter();
  const {
    handleSubmit,
    register,
    watch,
    formState: { errors },
  } = useForm({
    defaultValues: {
      name: "",
      lastName: "",
      email: "",
      phone: "",
      cedula: "",
      especialidad: "",
    },
    mode: "onBlur",
  });
  const submitData = async (data: any) => {
    try {
      setLoading(true);
      // Use proper typing from the interface
      const payload: IActivatePayload = {
        name: data.name,
        last_name: data.lastName,
        email: data.email,
        phone: data.phone,
        professional_id: data.cedula,
        specialization: data.especialidad,
      };

      const result = await activateProfile(payload);
      if (result) {
        setSuccessScreen(true);
      }
    } catch (error: any) {
      console.error("Error activating profile:", error);

      // Check if the error is related to email already in use
      if (
        error.response?.data?.email &&
        Array.isArray(error.response.data.email)
      ) {
        // Display toast notification for email already in use
        toast.error(
          "El correo electrónico ya está registrado. favor de dirigirse a iniciar sesión."
        );
      } else if (
        error.response?.data?.professional_id &&
        Array.isArray(error.response.data.professional_id)
      ) {
        // Display toast notification for email already in use
        toast.error(
          "La cédula profesional ya está registrada. favor de dirigirse a iniciar sesión."
        );
      } else {
        // Handle other errors
        toast.error(
          "Ha ocurrido un error al activar el perfil. Por favor intenta nuevamente."
        );
      }
    } finally {
      setLoading(false);
    }
  };

  // Function to convert input to uppercase as user types
  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.name !== "email") {
      e.target.value = e.target.value.toUpperCase();
    }
  };

  return (
    <div className="bg-gradient-to-b  from-[#FFFFFF00] to-[#847FCB] rounded-3xl p-6 shadow-lg">
      {/* Encabezado del formulario */}
      <div className="mb-6">
        <div className="py-2 inline-block mb-2 relative">
          <p
            className="absolute top-[-25px] left-3 text-white text-lg mb-1 w-[240px] text-center rounded-full px-2 py-1"
            style={{
              background:
                "linear-gradient(93.54deg, #FFFFFF -32.76%, #C9B28D 22.94%, #847FCB 63.53%, #423F65 132.76%)",
            }}
          >
            <strong>Registrate</strong> ahora
          </p>
          <h2 className="text-black text-[40px] font-bold">
            <span className="bg-white rounded-xl p-2 mr-1 pl-3">
              <span
                style={{
                  background:
                    "linear-gradient(91.65deg, #FFFFFF -30.52%, #C9B28D 23.68%, #847FCB 63.16%, #423F65 130.52%)",
                  WebkitBackgroundClip: "text",
                  WebkitTextFillColor: "transparent",
                  backgroundClip: "text",
                }}
              >
                {" "}
                Activa tu perfil{" "}
              </span>
            </span>{" "}
            de médico
          </h2>
        </div>
        <p className="text-black text-base">
          Completa el formulario{" "}
          <span className="text-black/80 text-xs">
            (un ejecutivo validará tu información)
          </span>
        </p>
      </div>

      {/* Formulario */}
      <form onSubmit={handleSubmit(submitData)} className="space-y-4">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          {/* Nombre */}
          <div>
            <label
              htmlFor="name"
              className={`block text-sm mb-1 ${
                errors.name
                  ? "text-red-400 font-[500]"
                  : "text-[#27348B] font-[500]"
              }`}
            >
              Nombre(s)*
            </label>
            <input
              id="name"
              placeholder="Tu nombre completo"
              {...register("name", {
                required: "El nombre es requerido",
              })}
              className={`w-full border ${
                errors.name
                  ? "border-red-400 focus:ring-red-400"
                  : "border-gray-300 focus:ring-blue-400"
              } rounded-md p-2 focus:outline-none focus:ring-2 transition-colors`}
              onChange={handleInputChange}
            />
            {errors.name && (
              <p className="mt-1 text-sm text-red-400 font-[500] flex items-center gap-1">
                <AlertTriangle className="h-4 w-4" />
                {errors.name.message}
              </p>
            )}
          </div>

          {/* Apellidos */}
          <div>
            <label
              htmlFor="lastName"
              className={`block text-sm mb-1 ${
                errors.lastName
                  ? "text-red-400 font-[500]"
                  : "text-[#27348B] font-[500]"
              }`}
            >
              Apellidos(s)*
            </label>
            <input
              id="lastName"
              placeholder="Tus apellidos"
              {...register("lastName", {
                required: "Los apellidos son requeridos",
              })}
              className={`w-full border ${
                errors.lastName
                  ? "border-red-400 focus:ring-red-400"
                  : "border-gray-300 focus:ring-blue-400"
              } rounded-md p-2 focus:outline-none focus:ring-2 transition-colors`}
              onChange={handleInputChange}
            />
            {errors.lastName && (
              <p className="mt-1 text-sm text-red-400 font-[500] flex items-center gap-1">
                <AlertTriangle className="h-4 w-4" />
                {errors.lastName.message}
              </p>
            )}
          </div>

          {/* Correo electrónico */}
          <div>
            <label
              htmlFor="email"
              className={`block text-sm mb-1 ${
                errors.email
                  ? "text-red-400 font-[500]"
                  : "text-[#27348B] font-[500]"
              }`}
            >
              Correo electrónico*
            </label>
            <input
              id="email"
              type="email"
              placeholder="Ingresa tu correo electrónico"
              {...register("email", {
                required: "El correo electrónico es requerido",
                pattern: {
                  value: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
                  message: "Ingresa un correo electrónico válido",
                },
              })}
              className={`w-full border ${
                errors.email
                  ? "border-red-400 focus:ring-red-400"
                  : "border-gray-300 focus:ring-blue-400"
              } rounded-md p-2 focus:outline-none focus:ring-2 transition-colors`}
              onChange={handleInputChange}
            />
            {errors.email && (
              <p className="mt-1 text-sm text-red-400 font-[500] flex items-center gap-1">
                <AlertTriangle className="h-4 w-4" />
                {errors.email.message}
              </p>
            )}
          </div>

          {/* Teléfono */}
          <div>
            <label
              htmlFor="phone"
              className={`block text-sm mb-1 ${
                errors.phone
                  ? "text-red-400 font-[500]"
                  : "text-[#27348B] font-[500]"
              }`}
            >
              Teléfono
            </label>
            <input
              id="phone"
              placeholder="Tu número de teléfono"
              {...register("phone", {
                required: "El teléfono es requerido",
                minLength: {
                  value: 10,
                  message: "El teléfono debe tener al menos 10 dígitos",
                },
              })}
              className={`w-full border ${
                errors.phone
                  ? "border-red-400 focus:ring-red-400"
                  : "border-gray-300 focus:ring-blue-400"
              } rounded-md p-2 focus:outline-none focus:ring-2 transition-colors`}
              onChange={handleInputChange}
              maxLength={10}
              onKeyPress={(e) => {
                if (!/[0-9]/.test(e.key)) {
                  e.preventDefault();
                }
              }}
            />
            {errors.phone && (
              <p className="mt-1 text-sm text-red-400 font-[500] flex items-center gap-1">
                <AlertTriangle className="h-4 w-4" />
                {errors.phone.message}
              </p>
            )}
          </div>

          {/* Cédula profesional */}
          <div>
            <label
              htmlFor="cedula"
              className={`block text-sm mb-1 ${
                errors.cedula
                  ? "text-red-400 font-[500]"
                  : "text-[#27348B] font-[500]"
              }`}
            >
              Cédula profesional*
            </label>
            <input
              id="cedula"
              placeholder="Ingresa tu cédula profesional"
              {...register("cedula", {
                required: "La cédula profesional es requerida",
                pattern: {
                  value: /^\d{7,8}$/,
                  message: "La cédula debe tener entre 7 y 8 dígitos",
                },
                validate: (value) =>
                  /^\d+$/.test(value) || "Solo se permiten números",
              })}
              className={`w-full border ${
                errors.cedula
                  ? "border-red-400 focus:ring-red-400"
                  : "border-gray-300 focus:ring-blue-400"
              } rounded-md p-2 focus:outline-none focus:ring-2 transition-colors`}
              onChange={handleInputChange}
              maxLength={8}
              onKeyPress={(e) => {
                if (!/[0-9]/.test(e.key)) {
                  e.preventDefault();
                }
              }}
            />
            {errors.cedula && (
              <p className="mt-1 text-sm text-red-400 font-[500] flex items-center gap-1">
                <AlertTriangle className="h-4 w-4" />
                {errors.cedula.message}
              </p>
            )}
          </div>

          {/* Especialidad médica */}
          <div>
            <label
              htmlFor="especialidad"
              className={`block text-sm mb-1 ${
                errors.especialidad
                  ? "text-red-400 font-[500]"
                  : "text-[#27348B] font-[500]"
              }`}
            >
              Especialidad médica*
            </label>
            <div className="relative">
              <input
                id="especialidad"
                placeholder="Tu especialidad"
                maxLength={30}
                {...register("especialidad", {
                  required: "La especialidad es requerida",
                  minLength: {
                    value: 3,
                    message: "La especialidad debe tener al menos 3 caracteres",
                  },
                  maxLength: {
                    value: 30,
                    message: "La especialidad no debe exceder 30 caracteres",
                  },
                })}
                className={`w-full border ${
                  errors.especialidad
                    ? "border-red-400 focus:ring-red-400"
                    : "border-gray-300 focus:ring-blue-400"
                } rounded-md p-2 focus:outline-none focus:ring-2 transition-colors`}
                onChange={handleInputChange}
              />
            </div>
            {errors.especialidad && (
              <p className="mt-1 text-sm text-red-400 font-[500] flex items-center gap-1">
                <AlertTriangle className="h-4 w-4" />
                {errors.especialidad.message}
              </p>
            )}
          </div>
        </div>

        {/* Nota sobre campos obligatorios */}
        <div className="text-white/80 text-xs text-center">
          Los campos marcados con (*) son obligatorios
        </div>

        {/* Aviso de contacto */}
        <div className="bg-amber-100 rounded-lg p-3 flex items-center gap-2 mt-4">
          <AlertTriangle className="h-5 w-5 text-amber-500 flex-shrink-0" />
          <p className="text-sm">
            Nuestro ejecutivo te contactará en un{" "}
            <strong>máximo de 48 horas</strong>
          </p>
        </div>

        {/* Botón de registro */}
        <button
          disabled={loading}
          className={`w-full flex items-center justify-center gap-2 h-12 rounded-3xl ${
            !watch("email") ||
            !watch("name") ||
            !watch("lastName") ||
            !watch("cedula") ||
            !watch("especialidad")
              ? "bg-gray-200 hover:bg-gray-300 text-gray-700"
              : "bg-black hover:bg-black/80 text-white"
          }`}
        >
          {loading ? (
            <Loader2 className="h-5 w-5 animate-spin" />
          ) : (
            <>
              Registrar como médico
              <ChevronRight className="h-5 w-5" />
            </>
          )}
        </button>
        <div className="flex justify-end text-sm ">
          <div className="text-white">
            Ya tienes una cuenta?{" "}
            <Link href="/" className="underline">
              Inicia sesión aquí
            </Link>
            .
          </div>
        </div>
      </form>
    </div>
  );
}
