import React from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { usePacients } from "@/store/pacients";
import { IPacient } from "@/types/Models/Pacient";
import { MdOutlineArrowBackIos } from "react-icons/md";
export default function CreateUser() {
  const {
    CreatePacient,
    editPacient,
    EditPacient,
    UpdatePacient,
    loadingAction,
    selectedPacient,
    SetStep,
  } = usePacients((state) => ({
    loadingAction: state.loadingAction,
    CreatePacient: state.CreatePacient,
    editPacient: state.editPacient,
    EditPacient: state.EditPacient,
    UpdatePacient: state.UpdatePacient,
    SetStep: state.SetStep,
    selectedPacient: state.selectedPacient,
  }));
  const submitData = async (data: IPacient) => {
    if (editPacient) UpdatePacient({ ...editPacient, ...data });
    else CreatePacient(data);
  };

  const getGender = (gender: string) => {
    switch (gender) {
      case "F":
        return "1";
      case "M":
        return "0";
      case "0":
        return "0";
      case "1":
        return "1";
      default:
        "";
    }
  };

  const fields: Field[] = [
    {
      label: `Ingresa la siguiente información para ${
        editPacient ? "editar" : "dar de alta"
      } al paciente.`,
      name: "title",
      required: true,
      type: "subtitle",
    },
    {
      label: "Nombre(s) *",
      name: "first_name",
      required: true,
      type: "text",
      default: editPacient?.first_name,
      width: 50,
    },
    {
      label: "Apellido Paterno *",
      name: "last_name1",
      required: true,
      type: "text",
      default: editPacient?.last_name1 || "",
      width: 50,
    },
    {
      label: "Apellido Materno *",
      name: "last_name2",
      required: true,
      type: "text",
      default: editPacient?.last_name2 || "",
      width: 50,
    },
    {
      label: "Correo electrónico para envio de recetas *",
      name: "email",
      required: true,
      type: "email",
      default: editPacient?.email || "",
      width: 50,
    },
    {
      label: "Teléfono celular *",
      name: "phone1",
      required: true,
      type: "text",
      default: editPacient?.phone1 || "",
      width: 50,
      maxFile: 10,
    },
    {
      label: "Teléfono fijo",
      name: "phone2",
      required: false,
      type: "text",
      default: editPacient?.phone2 || "",
      width: 50,
      maxFile: 10,
    },
    {
      label: "Seleccionar Género *",
      name: "gender",
      required: true,
      type: "select",
      options: [
        { label: "Masculino", value: "0" },
        { label: "Femenino", value: "1" },
        { label: "Indefinido", value: "2" },
      ],
      default: getGender(editPacient?.gender || ""),
      width: 50,
    },
    {
      label: "Fecha de nacimiento *",
      name: "birth_date",
      required: true,
      inputType: "date",
      type: "date",
      default: editPacient?.birth_date,
      width: 50,
    },
  ];
  return (
    <div className="">
      <div className="flex items-center  mb-4 p-2 ps-3 container-dashboard">
        <button
          onClick={() => {
            editPacient && EditPacient(null);
            SetStep(editPacient || selectedPacient ? 2 : 1);
          }}
          className="button-BlacK flex justify-center items-center p-2 w-[120px] "
        >
          <MdOutlineArrowBackIos size={20} />
          <p className="ms-1"> Regresar</p>
        </button>
      </div>
      <div className="  mb-4 p-2 ps-3 container-dashboard">
        <FormGenerator
          submitData={submitData}
          fields={fields}
          loading={loadingAction}
          buttonText="Guardar"
        />
      </div>
    </div>
  );
}
