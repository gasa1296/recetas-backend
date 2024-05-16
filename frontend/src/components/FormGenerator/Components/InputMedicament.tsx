import Image from "next/image";
import MedicineDefault from "@/assets/images/placeholder.jpg";
import { useState } from "react";
import { FaPen, FaTrash } from "react-icons/fa";
interface Props {
  medicine: any;
  disabled?: boolean;
  removeMedicament: any;
  setShow: any;
}
export function InputMedicament({
  medicine,
  disabled,
  removeMedicament,
  setShow,
}: Props) {
  const [error, setError] = useState(false);
  return (
    <div className=" cardRecipesMedicine flex-col lg:flex-row flex  justify-between items-center py-2 px-3  ">
      <div className="flex flex-col lg:flex-row justify-between  items-center">
        <div className=" pr-3 min-w-[220px]">
          {error ? (
            <Image
              src={MedicineDefault}
              alt="Picture"
              width={500}
              height={500}
              className="image-medicament"
            />
          ) : (
            <Image
              src={medicine.presentacion}
              alt="Picture"
              width={500}
              height={500}
              className="image-medicament"
              onError={({ currentTarget }) => {
                setError(true);
              }}
            />
          )}
        </div>
        <div className="border-stone-950">
          <p className="text-[#1A1A1A] font-bold text-[20px] ">
            {medicine.name}
          </p>
          <p className="text-[#141414] text-[16px] ">
            {medicine.new
              ? medicine.indications
              : `${medicine.dose} | ${medicine.frequency} | ${
                  medicine.duration
                } | ${medicine.way}  | ${medicine.quantity} | ${
                  medicine.add || ""
                }`}
          </p>
          {(medicine.new ||
            medicine.group === "Grupo II" ||
            medicine.group === "Grupo III") && (
            <div className="bg-[#FFBB32] px-4 py-2 mr-4">
              Atención: Para prescribir este medicamento se requiere firma
              autógrafa.
            </div>
          )}
        </div>
      </div>

      {!disabled && (
        <div className="mt-4 lg:mt-0">
          <button
            onClick={(e) => {
              e.preventDefault();
              removeMedicament(medicine.uuid);
            }}
            className="button-delete  p-1 w-[119px] "
          >
            <FaTrash color="#F23D4F" size={20} className="me-2" />{" "}
            <p>Eliminar</p>
          </button>
          <button
            onClick={(e) => {
              e.preventDefault();
              setShow(medicine);
            }}
            className="button-edit flex  w-[119px] p-1 mt-2 "
          >
            <FaPen color="#000000" size={20} className="me-2" /> <p>Editar</p>
          </button>
        </div>
      )}
    </div>
  );
}
