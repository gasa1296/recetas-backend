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
import { HiPlusSmall } from "react-icons/hi2";
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
      <div className="flex items-center  mb-2 p-2 ps-3  container-dashboard">
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

      <section className="container-Patiens  flex justify-center py-5   mb-4">
        <div className="w-[100%] px-8">
          <FormGenerator
            submitData={submitData}
            fields={fields}
            loading={false}
            renderButton={(handleSubmit) => (
              <div className="flex justify-center w-full  "></div>
            )}
          />
          <div className="px-2 mt-8 relative">
            <p className=" font-bold text-[#4B4B4B] text-[18px] text-start mb-6 pt-2">
              Buscar Medicamentos
            </p>

            <button
              onClick={() => SetStep(4)}
              className="flex justify-center items-center button-BlacK p-2 px-3 absolute right-0 top-0"
            >
              <FaPills size={25} />
              <span className="ps-2">Crear nuevo medicamento</span>
            </button>

            <SearchInput />
            <Component setStep={SetStep} nextStep={nextStep} />
          </div>
        </div>
      </section>
    </section>
  );
}
