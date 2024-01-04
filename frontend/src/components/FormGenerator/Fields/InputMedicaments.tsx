import React, { useState } from "react";
import Image from "next/image";
import medicineLogo from "@/assets/images/medicine.png";
import { FaPen, FaTrash, FaPlusCircle } from "react-icons/fa";
import ModalEditMedicine from "@/components/Modals/ModalEditMedicine";
import { Field } from "@/types/Generals/FormGenerator";

export default function InputMedicaments({
    label,
    validate,
    error,
    setError,
    watch,
    name,
    setValue,
    handleChange,
}: Field) {
    const values: any = watch();
    const [show, setShow] = useState(false);

    const closeModal = () => {
        setShow(false);
    };

    const openModal = () => {
        setShow(true);
    };

    const medicines = values[name]?.map((medicine: any) => ({
        presentacion: medicine.new
            ? ""
            : `https://s3-repositorio-cloudseus.s3.amazonaws.com/commerce/products/product_${medicine.uicodproducto}.png`,
        name: medicine.new ? medicine.name : medicine.vnombreproducto,
        information: medicine.new
            ? medicine.indications
            : medicine.vnombresal?.split(";").join(" "),
        uuid: medicine.uicodproducto,
        new: medicine.new,
        ...medicine,
    }));

    const removeMedicament = (medicamentUuid: string) => {
        const medicaments = [
            ...values[name]?.filter(
                (medicament: any) => medicament.uicodproducto !== medicamentUuid
            ),
        ];

        setValue(name, medicaments);

        handleChange && handleChange(medicaments);

        if (!medicaments.length) setError(name, "error");
    };

    const updateMedicament = (medicamentUuid: string, data: any) => {
        const medicaments = [
            ...values[name]?.map((medicament: any) => {
                if (medicament.uicodproducto === medicamentUuid) {
                    return {
                        ...medicament,
                        ...data,
                    };
                }
                return medicament;
            }),
        ];

        setValue(name, medicaments);
        handleChange && handleChange(medicaments);
    };
    return (
        <section className="w-full px-2">
            <p
                className={`font-bold text-[#4B4B4B] text-[20px] text-start mb-4 ${
                    error && "text-red-400"
                }`}
            >
                {label}
            </p>
            <ModalEditMedicine
                show={show}
                updateMedicament={updateMedicament}
                closeModal={closeModal}
            />

            {medicines.map((medicine: any, index: number) => {
                return (
                    <div
                        key={index}
                        className=" cardRecipesMedicine flex-col lg:flex-row flex  justify-between items-center py-2 px-3  "
                    >
                        <div className="flex flex-col lg:flex-row justify-between  items-center">
                            <div className=" pr-3 min-w-[220px]">
                                <Image
                                    src={medicine.presentacion || medicineLogo}
                                    alt="Picture"
                                    width={500}
                                    height={500}
                                    className="image-medicament"
                                />
                            </div>
                            <div className="border-stone-950">
                                <p className="text-[#1A1A1A] font-bold text-[20px] ">
                                    {medicine.name}
                                </p>
                                <p className="text-[#141414] text-[16px] ">
                                    {medicine.information}
                                </p>
                                {medicine.new && (
                                    <div className="bg-[#FFBB32] px-4 py-2 mr-4">
                                        Atención: Este medicamento no puede
                                        prescribirse en una receta médica
                                        electrónica, si continúa sólo podrá
                                        imprimirse la receta y requerirá la
                                        firma “autógrafa“.
                                    </div>
                                )}
                            </div>
                        </div>

                        <div className="mt-4 lg:mt-0">
                            <button
                                onClick={(e) => {
                                    e.preventDefault();
                                    removeMedicament(medicine.uuid);
                                }}
                                className="button-delete  p-1 w-[119px] "
                            >
                                <FaTrash
                                    color="#F23D4F"
                                    size={20}
                                    className="me-2"
                                />{" "}
                                <p>Eliminar</p>
                            </button>
                            <button
                                onClick={(e) => {
                                    e.preventDefault();
                                    setShow(medicine);
                                }}
                                className="button-edit flex  w-[119px] p-1 mt-2 "
                            >
                                <FaPen
                                    color="#000000"
                                    size={20}
                                    className="me-2"
                                />{" "}
                                <p>Editar</p>
                            </button>
                        </div>
                    </div>
                );
            })}

            <div className="flex justify-center items-center mt-3 ">
                <button
                    onClick={() => validate && validate("", {})}
                    className="button-add flex items-center w-[309px] px-3 p-3 "
                >
                    <FaPlusCircle color="#000000" size={20} className="me-2" />{" "}
                    <p>Agregar otro medicamento</p>
                </button>
            </div>
        </section>
    );
}
