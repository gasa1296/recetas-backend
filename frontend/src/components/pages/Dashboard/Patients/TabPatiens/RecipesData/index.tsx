import React, { useState } from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { getRecipeDate, getStatusName } from "@/utils/getDateFormat";
import { usePacients } from "@/store/pacients";
import { calculateAge } from "@/utils/getAge";
import { MdOutlineArrowBackIos, MdOutlineLocalPrintshop } from "react-icons/md";
import { HiOutlineDuplicate } from "react-icons/hi";
import toast from "react-hot-toast";
import LoadingModal from "@/components/Loading/LoadingModal";
import { useMedicamentStore } from "@/store/medicaments";
const baseUrl = process.env.NEXT_PUBLIC_BASE_URL;

export default function RecipesData({ nextStep, backStep }: any) {
  const submitData = async (data: any) => {
    nextStep();
  };

  const { DuplicateRecipe } = useMedicamentStore((state) => ({
    DuplicateRecipe: state.DuplicateRecipe,
  }));
  const { selectedRecipe, SetStep, SetTabStep } = usePacients((state) => ({
    selectedRecipe: state.selectedRecipe,
    SetStep: state.SetStep,
    SetTabStep: state.SetTabStep,
  }));

  const newMedicaments = JSON.parse(selectedRecipe?.add_med ?? "[]");

  const [downloadLoading, setDownloadLoading] = useState(false);
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
      default: getStatusName(selectedRecipe?.status ?? 0),
    },
    {
      label: "Folio",
      name: "folio",
      type: "text",
      width: 33,
      disabled: true,
      default: selectedRecipe?.code,
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
      maxFile: 10,
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
      default: Number(selectedRecipe?.temp),
    },
    {
      label: "Peso (kg)",
      name: "weight",
      type: "number",
      width: 33,
      disabled: true,
      default: Number(selectedRecipe?.weight),
    },
    {
      label: "Altura (cm)",
      name: "height",
      type: "number",
      width: 33,
      disabled: true,
      default: Number(selectedRecipe?.height),
    },
    {
      label: "Presión arterial",
      name: "pressure",
      type: "text",
      width: 33,
      disabled: true,
      default: Number(selectedRecipe?.pressure),
    },
    {
      label: "Saturación (%)",
      name: "saturation",
      type: "number",
      width: 33,
      disabled: true,
      default: Number(selectedRecipe?.saturation),
    },
    {
      label: "Frecuencia cardiaca (ppm)",
      name: "ppm",
      type: "number",
      width: 33,
      disabled: true,
      default: Number(selectedRecipe?.ppm),
    },
    {
      label: "Separation",
      name: "title",
      type: "separation",
    },

    {
      label: "Diagnóstico Médico: ",
      name: "diagnostic",
      required: false,
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
      default: [...newMedicaments, ...(selectedRecipe?.medicaments ?? [])],
    },
    {
      label: "Separation",
      name: "title",
      type: "separation",
    },
  ];
  console.log("first", selectedRecipe);

  const handlePrint = async () => {
    try {
      setDownloadLoading(true);
      const token: string | null = await localStorage.getItem("sessionToken");
      const response = await fetch(
        `${baseUrl}/api/prescription/${selectedRecipe?.id}/file`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (!response.ok) {
        throw new Error(`HTTP error: ${response.status}`);
      }

      const blob = new Blob([await response.blob()], {
        type: "application/pdf",
      });

      const blobUrl = URL.createObjectURL(blob);

      window.open(blobUrl, "_blank");

      setDownloadLoading(false);
    } catch (err) {
      setDownloadLoading(false);
      toast(
        "Favor de intentarlo nuevamente presionando el botón Imprimir/Visualizar PDF",
        {
          icon: "⚠️", // Icono unicode de advertencia (opcional)
          style: {
            border: "1px solid #ffa502", // Borde naranja
            padding: "16px", // Espaciado interno
            color: "#ffa502", // Color del texto naranja
          },
        }
      );
      console.error("Error al obtener el PDF:", err);
    }
  };

  const handleDuplicate = () => {
    if (selectedRecipe) {
      nextStep();
      SetTabStep(2);
      SetStep(2);
      DuplicateRecipe(
        [...newMedicaments, ...(selectedRecipe?.medicaments ?? [])],
        selectedRecipe
      );
    }
  };

  return (
    <section>
      {downloadLoading && <LoadingModal />}
      <div className="flex items-center justify-between  mb-4 p-2 ps-3 container-dashboard">
        <button
          onClick={() => SetStep(2)}
          className="button-BlacK flex justify-center items-center p-2 w-[120px] "
        >
          <MdOutlineArrowBackIos size={20} />
          <p className="ms-1"> Regresar</p>
        </button>
        <div className="flex">
          <button
            onClick={() => handleDuplicate()}
            className="button-BlacK flex justify-center items-center p-2 w-[120px] "
          >
            <HiOutlineDuplicate size={20} />
            <p className="ms-1"> Duplicar </p>
          </button>
          <button
            onClick={handlePrint}
            className="flex  justify-center items-center border button-print  mw-[15%] mx-3 h-full  p-1 py-2 px-10"
          >
            <MdOutlineLocalPrintshop size={18} />
            <p className="mx-2"> Imprimir / Visualizar PDF</p>
          </button>
        </div>
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
