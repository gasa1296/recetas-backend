import Loading from "@/components/Loading";
import LoadingModal from "@/components/Loading/LoadingModal";
import { useMedicamentStore } from "@/store/medicaments";
import { usePacients } from "@/store/pacients";
import { useRecipeStore } from "@/store/recipes";
import React, { useEffect } from "react";

import { FaRegEnvelope, FaChevronDown } from "react-icons/fa";
import { MdOutlineLocalPrintshop } from "react-icons/md";

export default function Send({ resetTab }: any) {
  const ResetPacients = usePacients((state) => state.ResetPacients);
  const ResetMedicaments = useMedicamentStore(
    (state) => state.ResetMedicaments
  );
  const {
    SendRecipeByEmail,
    handlePrint,
    loading,
    setEnableDownload,
    enableDownload,
    hasMissingSign,
    ClearRecipe,
    recipes,
  } = useRecipeStore((state) => ({
    recipes: state.recipes,
    SendRecipeByEmail: state.SendRecipeByEmail,
    loading: state.loading,
    ClearRecipe: state.ClearRecipe,
    handlePrint: state.handlePrint,
    setEnableDownload: state.setEnableDownload,
    enableDownload: state.enableDownload,
    hasMissingSign: state.hasMissingSign,
  }));
  useEffect(() => {
    ResetPacients();
    ResetMedicaments();

    if (hasMissingSign) setEnableDownload(true);

    setTimeout(() => {
      setEnableDownload(true);
    }, 30000);
  }, []);

  console.log("first", recipes);

  return (
    <section className=" ">
      {loading && <LoadingModal />}
      <div className="flex items-center justify-between border-Tab p-2 ps-3 mt-4">
        <div className="flex items-center ">
          <FaRegEnvelope color="#Fff " size={28} />
          <p className="text-[#fff] text-[26px] ms-3">Enviar</p>
        </div>

        <FaChevronDown size={28} color="#Fff " />
      </div>

      {enableDownload ? (
        <section className="container-Patiens px-3 md:px-8 py-10 text-center">
          <p className="text-[#1A1A1A] text-[24px] font-bold">
            Receta generada de forma exitosa
          </p>
          <p className="mt-8 text-[20px] text-[ #1A1A1A]">
            Su receta fue generada de forma exitosa, puede compartirla con su
            paciente mediante alguno de los siguientes medios:
          </p>

          {recipes.map((recipe: any, index: number) => (
            <div key={index} className="mt-10 pb-10">
              <p className="text-[#1A1A1A] text-[20px] font-bold">
                Receta: {recipe.groupType}
              </p>
              <div className=" flex flex-wrap justify-center items-center ">
                <button
                  onClick={() => handlePrint(recipe.id)}
                  className="flex  justify-center items-center border button-print  mw-[15%] mx-3 text-[20px] mt-4 p-1 px-10"
                >
                  <MdOutlineLocalPrintshop size={18} />
                  <p className="mx-2 "> Imprimir / Visualizar PDF</p>
                </button>
                {recipe.hasSign && (
                  <button
                    onClick={() => SendRecipeByEmail(recipe.id)}
                    className="flex items-center justify-center button-white mw-[20%] p-1 px-10 mx-3 mt-4"
                  >
                    <FaRegEnvelope color="#1A1A1A " size={18} />
                    <p className="mx-2 "> Enviar por correo</p>
                  </button>
                )}
              </div>
            </div>
          ))}

          <div className="block md:flex justify-center  py-6">
            <button
              onClick={() => {
                resetTab();
                ClearRecipe();
              }}
              className="button-BlacK w-full p-2 font-bold max-w-[660px] "
            >
              Terminar / Generar nueva receta
            </button>
          </div>
        </section>
      ) : (
        <section className="container-Patiens px-3 md:px-8 py-10 text-center">
          <Loading text="Creando tu receta..." />
        </section>
      )}
    </section>
  );
}
