import Image from "next/image";
import React from "react";
import medicineLogo from "@/assets/images/medicine.png";
import MedicineNotFound from "./MedicineNotFound";
import { useMedicamentStore } from "@/store/medicaments";
import { HiPlusSmall } from "react-icons/hi2";
export default function ResultMedicine({
  setStep,
  setShow,
}: {
  setStep: any;
  setShow: any;
}) {
  const { medicaments, SelectMedicament, SetStep, SetSearch } =
    useMedicamentStore((state) => ({
      medicaments: state.medicaments,
      SelectMedicament: state.SelectMedicament,
      SetStep: state.SetStep,
      SetSearch: state.SetSearch,
    }));
  const medicines = medicaments?.map((medicine) => ({
    presentacion: `https://s3-repositorio-cloudseus.s3.amazonaws.com/commerce/products/product_${medicine.uicodproducto}.png`,
    name: medicine.vnombreproducto,
    information: medicine.vnombresal?.split(";").join(" "),
    uuid: medicine.uicodproducto,
  }));

  return (
    <section className="mt-4  relative">
      <p className="text-[#4B4B4B] text-[16px] font-bold mt-3">
        Resultados de su búsqueda
      </p>

      {medicines?.length ? (
        <button
          onClick={() => SetStep(4)}
          className="flex justify-center items-center button-BlacK p-2 px-3 absolute right-0 top-0"
        >
          <HiPlusSmall size={25} />
          Nuevo medicamento
        </button>
      ) : null}

      <div className="flex flex-wrap justify-start mt-8 ">
        {medicines?.length ? (
          medicines?.map((medicine: any, index) => {
            return (
              <div className="card-medicine mt-2 relative mx-2">
                <Image
                  src={medicine.presentacion}
                  alt="Picture"
                  width={500}
                  height={500}
                  className="image-medicament"
                />

                <p className="text-[#4B4B4B] font-semibold text-[14px] ms-4 mt-4">
                  {medicine.name}
                </p>

                <p className="text-[#141414] text-[14px] ms-4">
                  {medicine.information.slice(0, 180)}
                  {medicine.information.length > 180 ? "..." : ""}
                </p>
                <div className="flex justify-center absolute w-full bottom-5">
                  <button
                    onClick={() => {
                      SelectMedicament(medicine.uuid);
                      SetSearch("");
                    }}
                    className="button-BlacK2 p-1 w-[80%] rounded-full mt-7"
                  >
                    Seleccionar
                  </button>
                </div>
              </div>
            );
          })
        ) : (
          <MedicineNotFound />
        )}
      </div>
    </section>
  );
}
