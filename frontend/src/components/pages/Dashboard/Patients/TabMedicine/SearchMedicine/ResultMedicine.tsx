import React from "react";
import MedicineNotFound from "./MedicineNotFound";
import { useMedicamentStore } from "@/store/medicaments";
import { HiPlusSmall } from "react-icons/hi2";
import Loading from "@/components/Loading";
import { ResultMedicineItem } from "./Components/ResultMedicineItem";
import { PopularMedicaments } from "../../PopularMedicaments";
import useScrollToTop from "@/hooks/useScrollToTop";
export default function ResultMedicine() {
  useScrollToTop();
  const { medicaments, SetStep, loadingAction } = useMedicamentStore(
    (state) => ({
      medicaments: state.medicaments,
      loadingAction: state.loading,
      SetStep: state.SetStep,
    })
  );
  const medicines = medicaments?.map((medicine) => ({
    presentacion: `https://s3-repositorio-cloudseus.s3.amazonaws.com/commerce/products/product_${medicine.uicodproducto}.png`,
    name: medicine.vnombreproducto,
    information: medicine.vnombresal?.split(";").join(" "),
    uuid: medicine.uicodproducto,
  }));

  if (loadingAction)
    return (
      <section className="mt-4  relative">
        <p className="text-[#4B4B4B] text-[16px] font-bold mt-3">
          Resultados de su búsqueda
        </p>
        <Loading />
      </section>
    );

  return (
    <section className="mt-4  relative">
      <p className="text-[#4B4B4B] text-[18px] font-bold mt-4">
        Resultados de su búsqueda
      </p>

      <div className="flex flex-wrap justify-start mt-4 ">
        {medicines?.length ? (
          medicines?.map((medicine: any) => {
            return (
              <ResultMedicineItem key={medicine.uuid} medicine={medicine} />
            );
          })
        ) : (
          <MedicineNotFound />
        )}
      </div>

      <PopularMedicaments />
    </section>
  );
}
