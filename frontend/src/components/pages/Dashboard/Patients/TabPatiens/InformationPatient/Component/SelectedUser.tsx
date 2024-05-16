import React from "react";
import TableRecipes from "./TableRecipes";
import FormGenerator from "@/components/FormGenerator";
import { usePacients } from "@/store/pacients";
import { Field } from "@/types/Generals/FormGenerator";
import { calculateAge } from "@/utils/getAge";
import { HiPlusSmall } from "react-icons/hi2";
import { MdEdit } from "react-icons/md";
export default function SelectedUser({ nextStep }: any) {
  const { selectedPacient, SetStep, EditPacient } = usePacients((state) => ({
    selectedPacient: state.selectedPacient,
    SetStep: state.SetStep,
    EditPacient: state.EditPacient,
  }));

  const submitData = async () => {
    //nextStep();
  };

  let phone1Parse;
  try {
    phone1Parse = JSON.parse(selectedPacient?.phone1 || "");
  } catch (error) {
    phone1Parse = [""];
  }

  const fields: Field[] = [
    {
      label: "Nombre(s)",
      name: "first_name",
      required: true,
      type: "text",
      width: 50,
      disabled: true,
      default: `${selectedPacient?.first_name} ${selectedPacient?.last_name1} ${selectedPacient?.last_name2}`,
    },
    {
      label: "Correo electrónico",
      name: "email",
      required: true,
      type: "email",
      width: 50,
      disabled: true,
      default: selectedPacient?.email,
    },
    {
      label: "Teléfono celular ",
      name: "phone1",
      //required: true,
      type: "multiPhone",
      width: 50,
      disabled: true,
      default: phone1Parse,
      maxFile: 10,
    },
    {
      label: "Edad *",
      name: "birth_date",
      //required: true,
      type: "number",
      width: 50,
      disabled: true,
      default: calculateAge(selectedPacient?.birth_date),
    },
  ];
  return (
    <div className="">
      <div className="mt-3 relative">
        <button
          onClick={() => SetStep(3)}
          className="flex justify-center items-center button-BlacK p-2 px-3 absolute right-0"
        >
          <HiPlusSmall size={25} />
          Nuevo paciente
        </button>

        <div className="px-2 full-width flex items-center mb-4">
          <p className=" font-bold text-[#000] text-[18px] text-start pr-2">
            Paciente
          </p>
          <button
            onClick={() => selectedPacient && EditPacient(selectedPacient)}
          >
            <MdEdit size={20} />
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
      </div>
      <TableRecipes nextStep={nextStep} />
    </div>
  );
}
