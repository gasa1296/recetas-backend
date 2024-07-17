import LoadingModal from "@/components/Loading/LoadingModal";
import { useMedicamentStore } from "@/store/medicaments";
import { usePacients } from "@/store/pacients";
import { useRecipeStore } from "@/store/recipes";
import React, { useEffect } from "react";

import { FaRegEnvelope, FaChevronDown } from "react-icons/fa";

import { RecipeSign } from "./RecipeSign";
import { Recipe } from "./Recipe";

export default function Send({ resetTab }: any) {
  const ResetPacients = usePacients((state) => state.ResetPacients);
  const ResetMedicaments = useMedicamentStore(
    (state) => state.ResetMedicaments
  );
  const {
    loading,
    setEnableDownload,
    enableDownload,
    hasMissingSign,
    ClearRecipe,
    recipes,
    hasDownload,
    emailLoading,
    loadingDownload,
  } = useRecipeStore((state) => ({
    recipes: state.recipes,
    loading: state.loading,
    emailLoading: state.emailLoading,
    loadingDownload: state.loadingDownload,
    ClearRecipe: state.ClearRecipe,
    hasDownload: state.hasDownload,
    setEnableDownload: state.setEnableDownload,
    enableDownload: state.enableDownload,
    hasMissingSign: state.hasMissingSign,
  }));
  useEffect(() => {
    ResetPacients();
    ResetMedicaments();

    setEnableDownload(true);
  }, []);

  const recipesReverse = [...recipes].reverse();

  return (
    <section className=" ">
      {loadingDownload && <LoadingModal text={"Descargando receta..."} />}
      {emailLoading && <LoadingModal text={"Enviando receta..."} />}
      <div className="flex items-center justify-between border-Tab p-2 ps-3 mt-4">
        <div className="flex items-center ">
          <FaRegEnvelope color="#Fff " size={28} />
          <p className="text-[#fff] text-[26px] ms-3">Enviar</p>
        </div>

        <FaChevronDown size={28} color="#Fff " />
      </div>

      <section className="container-Patiens px-3 md:px-8 py-10 text-center">
        {recipesReverse.find((recipe) => !recipe.hasSign) && (
          <>
            <p className="text-[#1A1A1A] text-[20px] font-bold">
              Receta sin firma electrónica generadas de manera exitosa y lista
              para imprimir
            </p>
            <Recipe recipes={recipesReverse} />
          </>
        )}

        {recipesReverse.find((recipe) => recipe.hasSign) && (
          <>
            <p className="text-[#1A1A1A] text-[20px] font-bold mt-10 max-w-[480px] mx-auto">
              {enableDownload
                ? "Receta con firma electrónica generada, certificada y enviada por correo"
                : "Receta con firma electrónica certificando"}
            </p>

            {recipesReverse.map((recipe: any) =>
              recipe.hasSign ? (
                <RecipeSign {...recipe} enableDownload={enableDownload} />
              ) : null
            )}
          </>
        )}

        <div className="block md:flex justify-center  py-6">
          <button
            onClick={() => {
              resetTab();
              ClearRecipe();
            }}
            disabled={!enableDownload || !hasDownload}
            className="button-BlacK w-full p-2 font-bold max-w-[660px] disabled:opacity-40 "
          >
            Terminar/Generar nueva receta
          </button>
        </div>
      </section>
    </section>
  );
}
