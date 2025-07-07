import Link from "next/link";

export const BenefitsBanner = () => {
  return (
    <div className="container col-span-12 w-full mx-auto py-8">
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
            Conoce cómo mejorar tu práctica con nuestros beneficios exclusivos.
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
  );
};
