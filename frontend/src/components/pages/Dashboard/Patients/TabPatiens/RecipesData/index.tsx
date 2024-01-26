import React from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { getRecipeDate, getStatusName } from "@/utils/getDateFormat";
import { useMedicamentStore } from "@/store/medicaments";
import { usePacients } from "@/store/pacients";
import { calculateAge } from "@/utils/getAge";
import Image from "next/image";
import medicineLogo from "@/assets/images/medicine.png";
import { MdOutlineArrowBackIos } from "react-icons/md";

export default function RecipesData({ nextStep, backStep }: any) {
  const submitData = async (data: any) => {
    nextStep();
  };
  const { selectedRecipe, SetStep } = usePacients((state) => ({
    selectedRecipe: state.selectedRecipe,
    SetStep: state.SetStep,
  }));

  const fields: Field[] = [
    {
      label: "Fecha",
      name: "date",
      required: true,
      type: "text",
      width: 33,
      disabled: true,
      default: getRecipeDate(selectedRecipe?.created_at),
    },
    {
      label: "Estado de la receta",
      name: "status",
      type: "text",
      width: 33,
      disabled: true,
      default: getStatusName(selectedRecipe?.status || 0),
    },
    {
      label: "Folio",
      name: "folio",
      type: "text",
      width: 33,
      disabled: true,
      default: selectedRecipe?.id,
    },

    {
      label: "Nombre(s)",
      name: "first_name",
      type: "text",
      width: 50,
      disabled: true,
      default: `${selectedRecipe?.patient?.first_name} ${selectedRecipe?.patient?.last_name1} ${selectedRecipe?.patient?.last_name2}`,
    },
    {
      label: "Correo electrónico",
      name: "email",
      type: "email",
      width: 50,
      disabled: true,
      default: selectedRecipe?.patient?.email,
    },
    {
      label: "Teléfono celular *",
      name: "phone1",
      type: "text",
      width: 50,
      disabled: true,
      default: selectedRecipe?.patient?.phone1,
    },
    {
      label: "Edad *",
      name: "birth_date",
      type: "number",
      width: 50,
      disabled: true,
      default: calculateAge(selectedRecipe?.patient?.birth_date),
    },

    {
      label: "Separation",
      name: "title",
      type: "separation",
    },
    {
      label: "Temperatura (°C)",
      name: "temp",
      type: "number",
      width: 33,
      disabled: true,
      default: selectedRecipe?.temp,
    },
    {
      label: "Peso (kg)",
      name: "weight",
      type: "number",
      width: 33,
      disabled: true,
      default: selectedRecipe?.weight,
    },
    {
      label: "Talla (cm)",
      name: "height",
      type: "number",
      width: 33,
      disabled: true,
      default: selectedRecipe?.height,
    },
    {
      label: "Presión arterial",
      name: "pressure",
      type: "number",
      width: 33,
      disabled: true,
      default: selectedRecipe?.pressure,
    },
    {
      label: "Saturación (%)",
      name: "saturation",
      type: "number",
      width: 33,
      disabled: true,
      default: selectedRecipe?.saturation,
    },
    {
      label: "Frecuencia cardiaca (ppm)",
      name: "ppm",
      type: "number",
      width: 33,
      disabled: true,
      default: selectedRecipe?.ppm,
    },
    {
      label: "Separation",
      name: "title",
      type: "separation",
    },

    {
      label: "Diagnóstico Médico: *",
      name: "diagnostic",
      required: true,
      type: "textarea",
      width: 100,
      disabled: true,
      default: selectedRecipe?.diagnostic,
    },
    {
      label: "Indicaciones médicas adicionales:",
      name: "add",
      type: "textarea",
      width: 100,
      disabled: true,
      default: selectedRecipe?.add,
    },
    {
      label: "Separation",
      name: "title",
      type: "separation",
    },
    {
      label: "Medicamentos agregados en su receta",
      name: "medicaments",
      required: true,
      type: "medicaments",
      disabled: true,
      default: selectedRecipe?.medicaments,
    },
    {
      label: "Separation",
      name: "title",
      type: "separation",
    },
  ];

  return (
    <section>
      <div className="flex items-center  mb-4 p-2 ps-3 container-dashboard">
        <button
          onClick={() => SetStep(2)}
          className="button-BlacK flex justify-center items-center p-2 w-[120px] "
        >
          <MdOutlineArrowBackIos size={20} />
          <p className="ms-1"> Regresar</p>
        </button>
      </div>
      <div className="  mb-4 p-2 ps-3 container-dashboard">
        <p className="text-[#1A1A1A] text-[18px] my-6 font-bold pl-2">
          Datos de la receta
        </p>
        <div>
          <FormGenerator
            submitData={submitData}
            fields={fields}
            loading={false}
            buttonText="Continuar"
            renderButton={(handleSubmit) => (
              <div className=" w-full px-2"></div>
            )}
          />

          <div className=" ml-2 flex items-start card-sing w-[100%] md:w-[40%] md:min-w-[400px] p-2 md:mr-5 mt-5">
            <label className="ms-4">
              <h5 className="title-card">{selectedRecipe?.room.name}</h5>
              <p className="text-card">{selectedRecipe?.room.address}</p>
              <p className="text-card">{selectedRecipe?.room.street}</p>
              <p className="text-card">{selectedRecipe?.room.state}</p>
            </label>
          </div>
        </div>
      </div>
    </section>
  );
}
