"use client";

import { useState, useEffect } from "react";
import { Home, Check } from "lucide-react";
import Link from "next/link";
import logo from "../../assets/LogoFESA.svg";

import Image from "next/image";

export default function RegistrationSuccess() {
  // Estado para el contador regresivo
  const [countdown, setCountdown] = useState({
    hours: 48,
    minutes: 0,
    seconds: 0,
  });

  // Efecto para actualizar el contador cada segundo
  useEffect(() => {
    const timer = setInterval(() => {
      setCountdown((prev) => {
        if (prev.seconds > 0) {
          return { ...prev, seconds: prev.seconds - 1 };
        } else if (prev.minutes > 0) {
          return { ...prev, minutes: prev.minutes - 1, seconds: 59 };
        } else if (prev.hours > 0) {
          return { hours: prev.hours - 1, minutes: 59, seconds: 59 };
        }
        return prev; // Si todo llega a cero, mantener en cero
      });
    }, 1000);

    // Limpiar el intervalo cuando el componente se desmonte
    return () => clearInterval(timer);
  }, []);

  return (
    <div className="col-span-6 xl:col-span-2 flex flex-col space-y-6">
      {/* Mensaje de éxito */}
      <div className="bg-[#CCEBC233] rounded-xl p-4 text-center relative">
        <div className="absolute -top-10 left-1/2 transform -translate-x-1/2">
          <Image
            src="/login/Exito.png"
            alt="Registro Exitoso"
            width={80}
            height={80}
            className="inline-flex"
            quality={90}
          />
        </div>
        <h2 className="text-2xl text-[#003480] font-bold mt-4 mb-2">
          ¡Registro de{" "}
          <span
            style={{
              background:
                "linear-gradient(91.65deg, #FFFFFF -30.52%, #C9B28D 23.68%, #847FCB 63.16%, #423F65 130.52%)",
              WebkitBackgroundClip: "text",
              WebkitTextFillColor: "transparent",
              backgroundClip: "text",
            }}
          >
            médico exitoso
          </span>
          !
        </h2>
        <p className="">
          Gracias por unirte a <strong>nuestra red comprometida</strong> con una
          práctica médica más eficiente y de calidad.
        </p>
      </div>

      {/* Contador y mensaje de contacto */}
      <div className="bg-orange-50 rounded-xl p-4">
        <div className="flex flex-col items-center md:flex-row gap-6">
          {/* Contenido: texto y contador */}
          <div className="flex-1">
            <div className="text-center md:text-left mb-4">
              <p className="text-center">
                Un ejecutivo te contactará en las{" "}
                <strong>próximas 48 horas</strong> para validar tu información.
              </p>
            </div>

            {/* Contador regresivo */}
            <div className="flex justify-center items-center gap-1 my-4">
              <div className="text-center mx-1">
                <div className="text-4xl font-bold">
                  {countdown.hours.toString().padStart(2, "0")}
                </div>
                <div className="text-sm mx-1">Horas</div>
              </div>
              <div className="text-4xl font-bold mx-1">:</div>
              <div className="text-center mx-1">
                <div className="text-4xl font-bold">
                  {countdown.minutes.toString().padStart(2, "0")}
                </div>
                <div className="text-sm mx-1">Minutos</div>
              </div>
              <div className="text-4xl font-bold mx-1">:</div>
              <div className="text-center mx-1">
                <div className="text-4xl font-bold">
                  {countdown.seconds.toString().padStart(2, "0")}
                </div>
                <div className="text-sm">Segundos</div>
              </div>
            </div>
          </div>
        </div>

        <p className="text-center text-gray-700 mt-4 bg-white rounded-lg p-2  shadow-sm">
          Una vez que el ejecutivo finalice la llamada, tu perfil se activará y
          podrás{" "}
          <Link href="/" className="font-bold  underline">
            iniciar sesión
          </Link>{" "}
          para disfrutar de todas nuestras herramientas.
        </p>
      </div>

      {/* Botón para ir al sitio */}
      <Link
        href="https://mcstaging.farmaciasespecializadas.com/"
        className="bg-black hover:bg-black/80 text-white py-4 px-6 rounded-3xl flex items-center justify-center gap-2 text-center"
      >
        Ir al sitio de Farmacias Especializadas
        <Home color="white" className="h-5 w-5" />
      </Link>
    </div>
  );
}
