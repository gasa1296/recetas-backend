import React, { useEffect } from "react";
import { FaPills } from "react-icons/fa";
import { MdOutlineArrowBackIos } from "react-icons/md";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import SearchInput from "./SearchInput";
import FindMedicine from "./FindMedicine";
import ResultMedicine from "./ResultMedicine";
import AddMedicationPrescription from "../AddMedicine/AddMedicationPrescription";
import AddNewMedicine from "../AddMedicine/AddNewMedicine";
import { usePacients } from "@/store/pacients";
import { useMedicamentStore } from "@/store/medicaments";
import { PopularMedicaments } from "../../PopularMedicaments";
export default function SearchMedicine({ nextStep, backStep }: any) {
  const { selectedPacient } = usePacients((state) => ({
    selectedPacient: state.selectedPacient,
  }));

  const { step, SetStep } = useMedicamentStore((state) => ({
    step: state.step,
    SetStep: state.SetStep,
  }));

  const submitData = async () => {
    nextStep();
  };
  const fields: Field[] = [
    {
      label: "Paciente",
      name: "confirmarContrasena",
      required: true,
      type: "subtitle",
    },
    {
      label: "Nombre",
      name: "name-Patient",
      required: true,
      type: "text",
      width: 48,
      disabled: true,
      default: `${selectedPacient?.first_name} ${selectedPacient?.last_name1} ${selectedPacient?.last_name2}`,
    },
    {
      label: "Correo electrónico",
      name: "email",
      required: true,
      type: "email",
      width: 48,
      disabled: true,
      default: selectedPacient?.email,
    },
  ];

  const screen: any = {
    1: FindMedicine,
    2: ResultMedicine,
    3: AddMedicationPrescription,
    4: AddNewMedicine,
  };

  const Component = screen[step];
  return (
    <section>
      <div className="flex items-center  mb-4 p-2 ps-3 container-dashboard">
        <button
          onClick={() => {
            SetStep(1);
            backStep();
          }}
          className="button-BlacK flex justify-center items-center p-2 w-[120px] "
        >
          <MdOutlineArrowBackIos size={20} />
          <p className="ms-1"> Regresar</p>
        </button>
      </div>
      <FormGenerator
        submitData={submitData}
        fields={fields}
        loading={false}
        renderButton={(handleSubmit) => (
          <div className="flex justify-center w-full  "></div>
        )}
      />

      <div className="flex items-center  border-Tab p-2 ps-3 mt-4">
        <FaPills color="#Fff " size={28} />
        <p className="text-[#fff] text-[26px] ms-3">Medicamento</p>
      </div>
      <section className="container-Patiens  flex justify-center py-5 ">
        <div className="w-[100%] px-8">
          <SearchInput />

          <Component setStep={SetStep} nextStep={nextStep} />
        </div>
      </section>
    </section>
  );
}
