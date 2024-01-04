import Image from "next/image";
import React from "react";
import medicineLogo from "@/assets/images/medicine.png";
import MedicineNotFound from "./MedicineNotFound";
import { useMedicamentStore } from "@/store/medicaments";
export default function ResultMedicine({
    setStep,
    setShow,
}: {
    setStep: any;
    setShow: any;
}) {
    const { medicaments, SelectMedicament } = useMedicamentStore((state) => ({
        medicaments: state.medicaments,
        SelectMedicament: state.SelectMedicament,
    }));
    const medicines = medicaments?.map((medicine) => ({
        presentacion: `https://s3-repositorio-cloudseus.s3.amazonaws.com/commerce/products/product_${medicine.uicodproducto}.png`,
        name: medicine.vnombreproducto,
        information: medicine.vnombresal?.split(";").join(" "),
        uuid: medicine.uicodproducto,
    }));

    return (
        <section className="mt-4 ">
            <p className="text-[#4B4B4B] text-[16px] font-bold mt-3">
                Resultados de su búsqueda
            </p>
            <div className="flex flex-wrap justify-start mt-4 ">
                {medicines && medicines.length ? (
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

                                <p className="text-[#4B4B4B] font-semibold text-[14px] ms-4 mt-2">
                                    {medicine.name}
                                </p>

                                <p className="text-[#141414] text-[14px] ms-4">
                                    {medicine.information}
                                </p>
                                <div className="flex justify-center absolute w-full bottom-5">
                                    <button
                                        onClick={() =>
                                            SelectMedicament(medicine.uuid)
                                        }
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
