import React from "react";
import Image from "next/image";
import { FaPlus, FaTrash } from "react-icons/fa";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { useMedicamentStore } from "@/store/medicaments";
import { IMedicament } from "@/types/Models/Medicament";
import useScrollToTop from "@/hooks/useScrollToTop";

export default function AddMedicationPrescription({ setStep }: any) {
  const { CreateMedicament, selectedMedicament } = useMedicamentStore(
    (state) => ({
      CreateMedicament: state.CreateMedicament,
      SetStep: state.SetStep,
      selectedMedicament: state.selectedMedicament,
    })
  );
  useScrollToTop();

  const submitData = async (data: IMedicament) => {
    CreateMedicament({ ...data, ...selectedMedicament });
  };

  const getVigencia = (vigencia?: string) => {
    switch (vigencia) {
      case "Grupo II":
        return "(Vigente por 30 dias)";
      case "Grupo III":
        return "(Vigente por 180 dias)";
      case "RESTRICCION ANTIBIOTICOS":
        return "(Vigente por duración del tratamiento)";
      default:
        return "";
    }
  };

  const getMaxAmount = (group?: string) => {
    if (group === "Grupo II" || group === "Group III") return true;

    return false;
  };

  const fields: Field[] = [
    {
      label: "Dosis (Sin abreviaturas) *",
      name: "dose",
      required: true,
      type: "text",
      width: 50,
      default: "",
    },
    {
      label: "Frecuencia (Sin abreviaturas) *",
      name: "frequency",
      required: true,
      type: "text",
      width: 50,
      default: "",
    },
    {
      label: `Duración del tratamiento (Sin abreviaturas) ${getVigencia(
        selectedMedicament?.clasificacionsa
      )} *`,
      name: "duration",
      required: true,
      type: "text",
      width: 50,
      default: "",
    },
    {
      label: "Via de administración (Sin abreviaturas) *",
      name: "way",
      required: true,
      type: "text",
      width: 50,
      default: "",
    },
    {
      label: "Indicaciones adicionales",
      name: "add",
      required: false,
      type: "text",
      width: 50,
      default: "",
    },
    {
      label: "Cantidad de cajas para cubrir el tratamiento *",
      name: "quantity",
      required: true,
      type: "number",
      width: 50,
      default: "",
      max: getMaxAmount(selectedMedicament?.clasificacionsa) ? 2 : 0,
      min: 1,
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
          focus
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
