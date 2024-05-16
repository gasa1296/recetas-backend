import React from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { LuFileCheck } from "react-icons/lu";
import {
  MdOutlineArrowBackIos,
  MdOutlineMedicalServices,
} from "react-icons/md";
import { usePacients } from "@/store/pacients";
import { calculateAge } from "@/utils/getAge";
import { getRecipeDate } from "@/utils/getDateFormat";
import { useMedicamentStore } from "@/store/medicaments";
import { validateSameObject } from "@/utils/isSameObject";
import { IConfirmRecipForm } from "@/types/Models/Medicament";
import { useRecipeStore } from "@/store/recipes";
import { GiWeight } from "react-icons/gi";
import { MdOutlineBloodtype } from "react-icons/md";
import { CiTempHigh } from "react-icons/ci";
import { SiOxygen } from "react-icons/si";
import { BsJournalMedical } from "react-icons/bs";
import { GiBodyHeight } from "react-icons/gi";
import { TbHeartRateMonitor } from "react-icons/tb";
import { useAuthStore } from "@/store/auth";

export default function ConfirmRecipes({ nextStep, backStep }: any) {
  const CreateRecipe = useRecipeStore((state) => state.CreateRecipe);
  const loading = useRecipeStore((state) => state.loading);
  const user = useAuthStore((state) => state.user);

  const {
    cardMedicament,
    SetAllCardMedicament,
    confirmRecipForm,
    SetConfirmForm,
  } = useMedicamentStore((state) => ({
    SetConfirmForm: state.SetConfirmForm,
    confirmRecipForm: state.confirmRecipForm,
    cardMedicament: state.cardMedicament,
    SetAllCardMedicament: state.SetAllCardMedicament,
  }));

  const submitData = async (data: any) => {
    handleSubmitRecipe();
  };

  const handleSubmitRecipe = async () => {
    const result = await CreateRecipe(
      {
        user_id: user?.id || "",
        patient_id: selectedPacient?.id,
        ...confirmRecipForm,
      } as any,
      cardMedicament
    );

    if (result) {
      if (result.missingSign) nextStep(4);
      else nextStep();
    }
  };

  const { selectedPacient } = usePacients((state) => ({
    selectedPacient: state.selectedPacient,
  }));

  let phone1Parse;
  try {
    phone1Parse = JSON.parse(selectedPacient?.phone1 || "");
  } catch (error) {
    phone1Parse = [""];
  }

  const fields: Field[] = [
    {
      label: "Fecha",
      name: "date",
      required: true,
      type: "text",
      width: 50,
      disabled: true,
      default: getRecipeDate(),
    },
    {
      label: "Folio",
      name: "folio",
      type: "text",
      width: 50,
      disabled: true,
    },
    {
      label: "Paciente",
      name: "title",
      required: true,
      type: "subtitle",
    },

    {
      label: "Nombre(s)",
      name: "first_name",
      type: "text",
      width: 50,
      disabled: true,
      default: `${selectedPacient?.first_name} ${selectedPacient?.last_name1} ${selectedPacient?.last_name2}`,
    },
    {
      label: "Correo electrónico",
      name: "email",
      type: "email",
      width: 50,
      disabled: true,
      default: selectedPacient?.email,
    },
    {
      label: "Teléfono celular ",
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
      default: calculateAge(selectedPacient?.birth_date),
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
          default: confirmRecipForm?.temp,
          Icon: CiTempHigh,
        },
        {
          label: "Peso (kg) (Opcional)",
          name: "weight",
          type: "number",
          width: 33,
          default: confirmRecipForm?.weight,
          Icon: GiWeight,
        },
        {
          label: "Altura (cm) (Opcional)",
          name: "height",
          type: "number",
          width: 33,
          default: confirmRecipForm?.height,
          Icon: GiBodyHeight,
        },
        {
          label: "Presión arterial (Opcional)",
          name: "pressure",
          type: "text",
          width: 33,
          default: confirmRecipForm?.pressure,
          Icon: MdOutlineBloodtype,
        },
        {
          label: "Saturación (%) (Opcional)",
          name: "saturation",
          type: "number",
          width: 33,
          default: confirmRecipForm?.saturation,
          Icon: SiOxygen,
        },
        {
          label: "Frecuencia cardiaca (ppm) (Opcional)",
          name: "ppm",
          type: "number",
          width: 33,
          default: confirmRecipForm?.ppm,
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
          max: 150,
          type: "textarea",
          width: 100,
          default: confirmRecipForm?.diagnostic,
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
          max: 500,
          width: 100,
          default: confirmRecipForm?.add,
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
      default: cardMedicament,
      handleChange: (values) => SetAllCardMedicament(values),
      validate: () => {
        backStep();
      },
    },

    {
      label: "Separation",
      name: "title",
      type: "separation",
    },
    {
      label: "Selecciona el consultorio para generar tu receta electrónica",
      name: "room_id",
      type: "room",
      required: true,
      width: 100,
      default: confirmRecipForm?.room_id,
    },
  ];

  return (
    <section className="">
      <div className="flex items-center  mb-4 p-2 ps-3 container-dashboard">
        <button
          onClick={() => backStep()}
          className="button-BlacK flex justify-center items-center p-2 w-[120px] "
        >
          <MdOutlineArrowBackIos size={20} />
          <p className="ms-1"> Regresar</p>
        </button>
      </div>
      <div className="flex items-center  border-Tab p-2 ps-3 mt-4">
        <LuFileCheck color="#Fff " size={28} />
        <p className="text-[#fff] text-[26px] ms-3">Confirmar receta</p>
      </div>
      <section className="container-Patiens  flex justify-center py-5 ">
        <div className="w-[100%] px-8">
          <p className="text-[#1A1A1A] text-[18px] mb-4">
            Consulta todos los datos agregados del paciente.
          </p>

          <FormGenerator
            submitData={submitData}
            fields={fields}
            loading={loading}
            buttonText="Continuar"
            onFormChange={(form) => {
              if (validateSameObject(confirmRecipForm as object, form)) {
                SetConfirmForm(form as IConfirmRecipForm);
              }
            }}
            renderButton={(handleSubmit) => (
              <div className=" w-full px-2">
                <button
                  disabled={loading}
                  onClick={(e) => {
                    e.preventDefault();
                    handleSubmit(submitData);
                  }}
                  className="button-BlacK disabled:opacity-75  w-full px-3 p-3 text-center mt-5 "
                >
                  Confirmar
                </button>
              </div>
            )}
          />
        </div>
      </section>
    </section>
  );
}
