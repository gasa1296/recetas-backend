import React from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { LuFileCheck } from "react-icons/lu";
import CardRecipesAdd from "./CardRecipesAdd";
import { MdOutlineArrowBackIos } from "react-icons/md";
import { usePacients } from "@/store/pacients";
import { calculateAge } from "@/utils/getAge";
import { getRecipeDate } from "@/utils/getDateFormat";
import { useMedicamentStore } from "@/store/medicaments";
import { validateSameObject } from "@/utils/isSameObject";
import { IConfirmRecipForm } from "@/types/Models/Medicament";
import { useRecipeStore } from "@/store/recipes";
import { useAuthStore } from "@/store/auth";
import LoadingModal from "@/components/Loading/LoadingModal";

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
      label: "Teléfono celular *",
      name: "phone1",
      type: "text",
      width: 50,
      disabled: true,
      default: selectedPacient?.phone1,
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
      label: "Temperatura (°C)",
      name: "temp",
      type: "number",
      width: 33,
      default: confirmRecipForm?.temp,
    },
    {
      label: "Peso (kg)",
      name: "weight",
      type: "number",
      width: 33,
      default: confirmRecipForm?.weight,
    },
    {
      label: "Altura (cm)",
      name: "height",
      type: "number",
      width: 33,
      default: confirmRecipForm?.height,
    },
    {
      label: "Presión arterial",
      name: "pressure",
      type: "text",
      width: 33,
      default: confirmRecipForm?.pressure,
    },
    {
      label: "Saturación (%)",
      name: "saturation",
      type: "number",
      width: 33,
      default: confirmRecipForm?.saturation,
    },
    {
      label: "Frecuencia cardiaca (ppm)",
      name: "ppm",
      type: "number",
      width: 33,
      default: confirmRecipForm?.ppm,
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
      default: confirmRecipForm?.diagnostic,
    },
    {
      label: "Indicaciones médicas adicionales:",
      name: "add",
      type: "textarea",
      width: 100,
      default: confirmRecipForm?.add,
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
