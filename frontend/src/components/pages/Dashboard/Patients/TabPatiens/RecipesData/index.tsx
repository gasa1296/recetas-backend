import React, { useState } from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { getRecipeDate, getStatusName } from "@/utils/getDateFormat";
import { usePacients } from "@/store/pacients";
import { calculateAge } from "@/utils/getAge";
import {
  MdOutlineArrowBackIos,
  MdOutlineLocalPrintshop,
  MdOutlineMedicalServices,
} from "react-icons/md";
import { HiOutlineDuplicate } from "react-icons/hi";
import LoadingModal from "@/components/Loading/LoadingModal";
import { useMedicamentStore } from "@/store/medicaments";
import { useRecipeStore } from "@/store/recipes";
import { FaRegEnvelope, FaWhatsapp } from "react-icons/fa";
import { GiWeight } from "react-icons/gi";
import { MdOutlineBloodtype } from "react-icons/md";
import { CiTempHigh } from "react-icons/ci";
import { SiOxygen } from "react-icons/si";
import { GiBodyHeight } from "react-icons/gi";
import { TbHeartRateMonitor } from "react-icons/tb";
import { BsJournalMedical } from "react-icons/bs";
import useScrollToTop from "@/hooks/useScrollToTop";
const baseUrl = process.env.NEXT_PUBLIC_BASE_URL;

export default function RecipesData({ nextStep, backStep }: any) {
  const submitData = async (data: any) => {
    nextStep();
  };

  useScrollToTop();

  const { DuplicateRecipe } = useMedicamentStore((state) => ({
    DuplicateRecipe: state.DuplicateRecipe,
  }));
  const { selectedRecipe, SetStep, SetTabStep } = usePacients((state) => ({
    selectedRecipe: state.selectedRecipe,
    SetStep: state.SetStep,
    SetTabStep: state.SetTabStep,
  }));

  const { handlePrint, SendRecipeByEmail, sendRecipeByWhatsapp } =
    useRecipeStore((state) => ({
      handlePrint: state.handlePrint,
      SendRecipeByEmail: state.SendRecipeByEmail,
      sendRecipeByWhatsapp: state.SendRecipeByWhatsapp,
    }));

  const newMedicaments = JSON.parse(selectedRecipe?.add_med ?? "[]");

  const [downloadLoading, setDownloadLoading] = useState(false);
  const [emailLoading, setEmailLoading] = useState(false);

  let phone1Parse;
  try {
    phone1Parse = JSON.parse(selectedRecipe?.patient?.phone1 || "");
  } catch (error) {
    phone1Parse = [""];
  }
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
      type: "multiPhone",
      width: 50,
      disabled: true,
      default: phone1Parse,
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
      label: "Información adicional del paciente",
      name: "title",
      type: "collapse",
      form: [
        {
          label: "Temperatura (°C) (Opcional)",
          name: "temp",
          type: "number",
          width: 33,
          disabled: true,
          default: Number(selectedRecipe?.temp),
          Icon: CiTempHigh,
        },
        {
          label: "Peso (kg) (Opcional)",
          name: "weight",
          type: "number",
          width: 33,
          disabled: true,
          default: Number(selectedRecipe?.weight),
          Icon: GiWeight,
        },
        {
          label: "Altura (cm) (Opcional)",
          name: "height",
          type: "number",
          width: 33,
          disabled: true,
          default: Number(selectedRecipe?.height),
          Icon: GiBodyHeight,
        },
        {
          label: "Presión arterial (Opcional)",
          name: "pressure",
          type: "text",
          width: 33,
          disabled: true,
          default: selectedRecipe?.pressure,
          Icon: MdOutlineBloodtype,
        },
        {
          label: "Saturación (%) (Opcional)",
          name: "saturation",
          type: "number",
          width: 33,
          disabled: true,
          default: Number(selectedRecipe?.saturation),
          Icon: SiOxygen,
        },
        {
          label: "Frecuencia cardiaca (ppm) (Opcional)",
          name: "ppm",
          type: "number",
          width: 33,
          disabled: true,
          default: Number(selectedRecipe?.ppm),
          Icon: TbHeartRateMonitor,
        },
      ],
    },

    {
      label: "Separation",
      name: "title",
      type: "separation",
    },

    {
      label: "Diagnóstico Médico",
      name: "title",
      type: "collapse",
      form: [
        {
          label: "Diagnóstico Médico (Opcional): ",
          name: "diagnostic",
          required: false,
          type: "textarea",
          width: 100,
          max: 150,
          disabled: true,
          default: selectedRecipe?.diagnostic,
          Icon: BsJournalMedical,
        },
      ],
    },
    {
      label: "Separation",
      name: "title",
      type: "separation",
    },

    {
      label: "Indicaciones médicas adicionales",
      name: "title",
      type: "collapse",
      form: [
        {
          label: "Indicaciones médicas adicionales (Opcional):",
          name: "add",
          type: "textarea",
          width: 100,
          max: 500,
          disabled: true,
          default: selectedRecipe?.add,
          Icon: MdOutlineMedicalServices,
        },
      ],
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

  const notHaveSign =
    selectedRecipe?.medicaments.find(
      (medicament) =>
        medicament.group === "Grupo II" || medicament.group === "Grupo III"
    ) || JSON.parse(selectedRecipe?.add_med || "").length;

  return (
    <section>
      {downloadLoading && <LoadingModal text={"Descargando receta..."} />}
      {emailLoading && <LoadingModal text={"Enviando receta..."} />}
      <div className="flex  items-center justify-between  mb-4 p-2 ps-3 container-dashboard">
        <button
          onClick={() => SetStep(2)}
          className="button-BlacK flex justify-center items-center p-2 w-[120px] "
        >
          <MdOutlineArrowBackIos size={20} />
          <p className="ms-1"> Regresar</p>
        </button>
      </div>

      <div className="flex  items-center justify-between  mb-4 p-2 ps-3 container-dashboard">
        <div className="flex flex-wrap justify-center items-center w-full ">
          <button
            onClick={() => handleDuplicate()}
            className="button-BlacK flex justify-center items-center p-2 w-[120px] mt-4 "
          >
            <HiOutlineDuplicate size={20} />
            <p className="ms-1"> Duplicar </p>
          </button>
          <button
            onClick={async () => {
              setDownloadLoading(true);
              await handlePrint(
                selectedRecipe?.id,
                selectedRecipe?.document_id
              );
              setDownloadLoading(false);
            }}
            className="flex flex-wrap justify-center items-center border button-print  mw-[15%] mx-3 h-full  p-1 py-2 px-10 mt-4"
          >
            <MdOutlineLocalPrintshop size={18} />
            <p className="mx-2"> Imprimir / Visualizar PDF</p>
          </button>

          {!notHaveSign && (
            <>
              <button
                onClick={async () => {
                  setEmailLoading(true);
                  await SendRecipeByEmail(selectedRecipe?.id);

                  setEmailLoading(false);
                }}
                className="flex flex-wrap  items-center justify-center button-white mw-[20%] p-1 px-10 mx-3 mt-4"
              >
                <FaRegEnvelope color="#1A1A1A " size={18} />
                <p className="mx-2 "> Enviar por correo</p>
              </button>
              <button
                onClick={async () => {
                  setEmailLoading(true);
                  await sendRecipeByWhatsapp(selectedRecipe?.id);

                  setEmailLoading(false);
                }}
                className="flex items-center border justify-center button-whatsapp  mw-[15%] text-[20px] mt-4 p-1 mx-3 px-10"
              >
                <FaWhatsapp color="white" size={20} className="" />
                <p className="mx-2 "> Enviar por Whatsapp</p>
              </button>
            </>
          )}
        </div>
      </div>
      <div className="  mb-4 p-2 ps-3 container-dashboard">
        <p className="text-[#1A1A1A] text-[18px] my-6 font-bold pl-2 mt-4">
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
