import React, { useState } from "react";
import Image from "next/image";
import medicineLogo from "@/assets/images/medicine.png";
import { FaPen, FaTrash, FaPlusCircle } from "react-icons/fa";
import ModalEditMedicine from "@/components/Modals/ModalEditMedicine";

export default function CardRecipesAdd({ nextStep, backStep }: any) {
    const [show, setShow] = useState(false);

    const closeModal = () => {
        setShow(false);
    };

    const openModal = () => {
        setShow(true);
    };
    const medicines = [
        {
            presentacion: medicineLogo,
            nombre: "Dirpasid",
            NombreMedicine: "",
            informations:
                " Tomar vía oral, 1 tableta antes de cada alimento, por 5 días, Cada 24 horas ",
        },
    ];
    return (
        <section className=" ">
            <ModalEditMedicine
                show={show}
                closeModal={closeModal}
                openModal={openModal}
            />
            <div className="cardRecipesMedicine  ">
                {medicines.map((medicine, index) => {
                    return (
                        <div className=" flex justify-between items-center   ">
                            <div className="flex  items-center ">
                                <div className="border">
                                    {medicine.presentacion && (
                                        <Image
                                            src={medicine.presentacion}
                                            alt="Picture"
                                            className="border h-[141px] w-[151px]  "
                                        />
                                    )}
                                </div>

                                <div className="border-stone-950">
                                    <p className="text-[#1A1A1A] font-bold text-[24px] ms-4 mt-2">
                                        {medicine.nombre}
                                    </p>
                                    <p className="text-[#141414] text-[20px] ms-4 mt-1">
                                        {medicine.NombreMedicine}
                                    </p>
                                    <p className="text-[#141414] text-[20px] ms-4">
                                        {medicine.informations}
                                    </p>
                                </div>
                            </div>

                            <div className="block border mx-10">
                                <button className="button-delete  p-1 w-[119px] ">
                                    <FaTrash
                                        color="#F23D4F"
                                        size={20}
                                        className="me-2"
                                    />{" "}
                                    <p>Eliminar</p>
                                </button>
                                <button
                                    onClick={() => {
                                        setShow(true);
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
            </div>
            <div className="flex justify-center items-center mt-3 ">
                <button
                    onClick={() => {
                        backStep();
                    }}
                    className="button-add flex items-center w-[309px] px-3 p-3 "
                >
                    <FaPlusCircle color="#000000" size={20} className="me-2" />{" "}
                    <p>Agregar otro medicamento</p>
                </button>
            </div>
            <button
                onClick={() => {
                    nextStep();
                }}
                className="button-BlacK  w-full px-3 p-3 text-center mt-5 "
            >
                Confirmar
            </button>
        </section>
    );
}
