import React from "react";
import Image from "next/image";
import medicineLogo from "@/assets/images/medicine.png";
import { FaPlus, FaTrash } from "react-icons/fa";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { useMedicamentStore } from "@/store/medicaments";
import { IMedicament } from "@/types/Models/Medicament";

export default function AddMedicationPrescription({ setStep }: any) {
  const { CreateMedicament, selectedMedicament } = useMedicamentStore(
    (state) => ({
      CreateMedicament: state.CreateMedicament,
      SetStep: state.SetStep,
      selectedMedicament: state.selectedMedicament,
    })
  );

  const submitData = async (data: IMedicament) => {
    CreateMedicament({ ...data, ...selectedMedicament });
  };
  const fields: Field[] = [
    {
      label: "Dosis *",
      name: "dose",
      required: true,
      type: "text",
      width: 50,
      default: "",
    },
    {
      label: "Frecuencia *",
      name: "frequency",
      required: true,
      type: "text",
      width: 50,
      default: "",
    },
    {
      label: "Duración *",
      name: "duration",
      required: true,
      type: "text",
      width: 50,
      default: "",
    },
    {
      label: "Via de administración *",
      name: "way",
      maxFile: 1,
      required: true,
      type: "text",
      width: 50,
      default: "",
    },
    {
      label: "Indicaciones adicionales *",
      name: "add",
      maxFile: 1,
      required: true,
      type: "text",
      width: 50,
      default: "",
    },
    {
      label: "Cantidad total de medicamento por tratamiento *",
      name: "quantity",
      maxFile: 1,
      required: true,
      type: "number",
      width: 50,
      default: "",
    },
  ];

  const medicines = [
    {
      presentacion: `https://s3-repositorio-cloudseus.s3.amazonaws.com/commerce/products/product_${selectedMedicament?.uicodproducto}.png`,
      name: selectedMedicament?.vnombreproducto,
      information: selectedMedicament?.vnombresal?.split(";").join(" "),
      uuid: selectedMedicament?.uicodproducto,
    },
  ];
  return (
    <section className="lg:px-9 ">
      <div className="flex flex-col md:flex-row md:justify-between items-center ">
        {medicines.map((medicine, index) => {
          return (
            <div className=" mt-2 flex flex-col md:flex-row justify-center items-center ">
              <div className="">
                {medicine.presentacion && (
                  <Image
                    src={medicine.presentacion}
                    alt="Picture"
                    width={500}
                    height={500}
                    className="image-medicament"
                  />
                )}
              </div>

              <div className="border-stone-950">
                <p className="text-[#1A1A1A] font-bold text-[24px] ms-4 mt-2">
                  {medicine.name}
                </p>
                <p className="text-[#141414] text-[20px] ms-4 mt-1">
                  {medicine.information}
                </p>
              </div>
            </div>
          );
        })}
        <div className="flex h-[5%]  mb-4 p-2 ps-3">
          <button
            onClick={() => {
              setStep(2);
            }}
            className="button-BlacK flex justify-center items-center p-2 w-[120px] "
          >
            <FaTrash size={20} /> <p className="ms-1"> Eliminar</p>{" "}
          </button>
        </div>
      </div>
      <div className="mt-4 ">
        <FormGenerator
          submitData={submitData}
          fields={fields}
          loading={false}
          buttonText="Continuar"
          renderButton={(handleSubmit) => (
            <div className="flex justify-center w-full  ">
              <button
                className="button-BlacK disabled:opacity-40 flex justify-center items-center border-black border-2 p-1 text-black rounded-lg w-full mx-3 py-2 my-4"
                type="submit"
              >
                <FaPlus color="#fbfbfb" className="me-3" size={20} />
                Agregar medicamento a la receta
              </button>
            </div>
          )}
        />
      </div>
    </section>
  );
}
