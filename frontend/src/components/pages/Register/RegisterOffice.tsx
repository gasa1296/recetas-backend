import FormGenerator from "@/components/FormGenerator";
import { useRegisterStore } from "@/store/register";
import { Field } from "@/types/Generals/FormGenerator";
import { IForm3 } from "@/types/Store/Register";
import { validateSameObject } from "@/utils/isSameObject";

import React from "react";
import MexicoStates from "@/utils/constants/mexico-states.json";
import useScrollToTop from "@/hooks/useScrollToTop";
import { useRoomsStore } from "@/store/rooms";
import { RoomArraySchema } from "./helper";

export default function RegisterOffice({ nextStep, backStep }: any) {
  const setForm3 = useRegisterStore((state) => state.setForm3);
  const form3 = useRegisterStore((state) => state.form3);
  const submitData = async () => {
    nextStep();
  };

  const roomDesigns = useRoomsStore((state) => state.roomDesigns);

  useScrollToTop();

  const fields: Field[] = [
    {
      label: "Dirección del consultorio principal",
      name: "title",
      required: true,
      type: "title",
      width: 100,
    },

    {
      label: "",
      name: "rooms",
      type: "subForm",
      buttonAddText: "Agregar otro consultorio",
      maxFile: 1,
      width: 100,
      default: form3?.rooms ?? [
        {
          name: "",
          zip: "",
          street: "",
          colony: "",
          state: "",
          delegation: "",
          n_exterior: "",
          n_interior: "",
          address: "",
          phone: "",
          file: null,
          design: null,
        },
      ],
      form: [
        {
          label: "Dirección del consultorio",
          name: "title",
          required: true,
          type: "title",
          width: 100,
        },
        {
          label: "Nombre del consultorio",
          name: "name",
          moreOne: true,
          required: true,
          type: "text",
          width: 50,
          subFormKey: "name",
          default: form3?.rooms ?? "",
        },
        {
          label: "Código Postal *",
          name: "zip",
          required: true,
          type: "number",
          width: 50,
          minDigit: 5,
          min: 0,
          subFormKey: "zip",
          default: form3?.rooms ?? "",
        },
        {
          label: "Calle *",
          name: "street",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "street",
          default: form3?.rooms ?? "",
        },
        {
          label: "Colonia *",
          name: "colony",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "colony",
          default: form3?.rooms ?? "",
        },
        {
          label: "Estado *",
          name: "state",
          required: true,
          type: "selectSearch",
          width: 50,
          options: MexicoStates,
          subFormKey: "state",
          default: form3?.rooms ?? "",
        },
        {
          label: "Delegación o Municipio *",
          name: "delegation",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "delegation",
          default: form3?.rooms ?? "",
        },
        {
          label: "Número exterior *",
          name: "n_exterior",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "n_exterior",
          default: form3?.rooms ?? "",
        },
        {
          label: "Número interior (Opcional)",
          name: "n_interior",
          required: false,
          type: "text",
          width: 50,
          subFormKey: "n_interior",
          default: form3?.rooms ?? "",
        },
        {
          label: "Piso / Nº de local / No. Consultorio (Opcional)",
          name: "address",
          required: false,
          type: "text",
          width: 50,
          subFormKey: "address",
          default: form3?.rooms ?? "",
        },
        {
          label: "Teléfono de consultorio (Opcional)",
          name: "phone",
          required: false,
          type: "text",
          width: 50,
          maxFile: 10,
          subFormKey: "phone",
          default: form3?.rooms ?? "",
        },

        {
          label: "Agrega el logotipo de su consultorio ",
          name: "files",
          required: false,
          type: "file",
          width: 100,
          subFormKey: "files",
          default: form3?.rooms ?? "",
          maxFile: 1,
        },

        {
          label: "Diseño de la Receta",
          name: "title3",
          required: true,
          type: "title",
          width: 100,
        },

        {
          label: "Elija el diseño para su receta de las siguientes opciones:",
          name: "design",
          required: false,
          type: "recetas",
          width: 100,
          subFormKey: "design",
          default: form3?.rooms ?? "",
          recetasOptions: [
            { image: "/recetas/Receta1.png", value: roomDesigns[0] },
            { image: "/recetas/Receta2.png", value: roomDesigns[1] },
            { image: "/recetas/Receta3.png", value: roomDesigns[2] },
          ],
        },
      ],
    },
  ];
  return (
    <section className="max-w-[1000px] mx-auto px-3 md:px-2">
      <h2 className="text-center text-[#1A1A1A] text-[28px] mt-5 font-medium mb-7">
        Ingrese la información de su consultorio
      </h2>

      <div className="flex   ">
        <div className=" w-full">
          <FormGenerator
            buttonText="Continuar"
            submitData={submitData}
            fields={fields}
            loading={false}
            schema={RoomArraySchema}
            onFormChange={(form) => {
              if (validateSameObject(form3 as object, form)) {
                setForm3(form as IForm3);
              }
            }}
            renderButton={(handleSubmit) => (
              <div className="flex justify-center w-full ">
                <button
                  onClick={(e) => {
                    e.preventDefault();
                    backStep();
                  }}
                  disabled={false}
                  className="bg-white font-bold border-black border-2 p-3 text-black rounded-lg w-60 mx-3 block my-4"
                  type="submit"
                >
                  Anterior
                </button>
                <button
                  onClick={(e) => {
                    e.preventDefault();
                    handleSubmit();
                  }}
                  disabled={false}
                  className="bg-[#000000] p-3 text-[#EBF4F8] rounded-lg w-60 mx-3 block my-4"
                  type="submit"
                >
                  Continuar
                </button>
              </div>
            )}
          />
        </div>
      </div>
    </section>
  );
}
