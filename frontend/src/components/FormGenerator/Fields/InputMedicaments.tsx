import React, { useState } from "react";

import { FaPlusCircle } from "react-icons/fa";
import ModalEditMedicine from "@/components/Modals/ModalEditMedicine";
import { Field } from "@/types/Generals/FormGenerator";
import { InputMedicament } from "../Components/InputMedicament";

export default function InputMedicaments({
  label,
  validate,
  error,
  setError,
  watch,
  name,
  setValue,
  disabled,
  handleChange,
}: Field) {
  const values: any = watch();
  const [show, setShow] = useState(false);

  const closeModal = () => {
    setShow(false);
  };

  const medicines = values[name]?.map((medicine: any) => ({
    presentacion: medicine.new
      ? ""
      : `https://s3-repositorio-cloudseus.s3.amazonaws.com/commerce/products/product_${
          medicine.uicodproducto || medicine.medicament_id
        }.png`,
    name: medicine.new ? medicine.name : medicine.vnombreproducto,
    uuid: medicine.uicodproducto || medicine.medicament_id,
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
          <InputMedicament
            removeMedicament={removeMedicament}
            setShow={setShow}
            disabled={disabled}
            medicine={medicine}
            key={index}
          />
        );
      })}

      {!disabled && (
        <div className="flex justify-center items-center mt-3 ">
          <button
            onClick={() => validate && validate("", {})}
            className="button-add flex items-center w-[309px] px-3 p-3 "
          >
            <FaPlusCircle color="#000000" size={20} className="me-2" />{" "}
            <p>Agregar otro medicamento</p>
          </button>
        </div>
      )}
    </section>
  );
}
