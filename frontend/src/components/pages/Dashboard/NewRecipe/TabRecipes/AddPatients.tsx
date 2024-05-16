import React from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
export default function AddPatients({ nextStep, backStep }: any) {
  const submitData = async () => {
    nextStep();
  };
  const fields: Field[] = [
    {
      label: "Buscar paciente",
      name: "confirmarContrasena",
      required: true,
      type: "title",
    },
    {
      name: "name",
      required: true,
      type: "text",
      width: 100,
    },
    {
      label: "Ingresa la siguiente información para dar de alta al paciente.",
      name: "confirmarContrasena",
      required: true,
      type: "title",
    },
    {
      label: "Nombre(s) *",
      name: "name",
      required: true,
      type: "text",
      width: 50,
    },
    {
      label: "Apellido Paterno *",
      name: "Last-name",
      required: true,
      type: "text",
      width: 50,
    },
    {
      label: "Apellido Materno *",
      name: "Mother-last-name",
      required: true,
      type: "text",
      width: 50,
    },

    {
      label: "Correo electrónico *",
      name: "email",
      required: true,
      type: "email",
      width: 50,
    },
    {
      label: "Teléfono celular *",
      name: "phone",
      required: true,
      type: "text",
      width: 50,
      maxFile: 10,
    },
    {
      label: "Teléfono fijo (Opcional)",
      name: "Landline",
      required: false,
      type: "text",
      width: 50,
      maxFile: 10,
    },
    {
      label: "Seleccionar Género (Opcional)",
      name: "Select-Genre",
      required: false,
      type: "select",
      width: 50,
    },
    {
      label: "Fecha de nacimiento *",
      name: "Birthdate  ",
      required: true,
      inputType: "date",
      type: "date",
      width: 50,
    },
  ];
  return (
    <section className="px-12 p-3 bg-[#fff] mt-5">
      <FormGenerator
        submitData={submitData}
        fields={fields}
        loading={false}
        buttonText="Continuar"
      />
    </section>
  );
}
