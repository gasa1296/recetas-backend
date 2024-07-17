import React from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { FaTrash, FaPlus } from "react-icons/fa";
import { useMedicamentStore } from "@/store/medicaments";
import { IMedicament } from "@/types/Models/Medicament";
import useScrollToTop from "@/hooks/useScrollToTop";

export default function AddNewMedicine({ nextStep, backStep }: any) {
  const { CreateMedicament, SetStep, search, SetSearch } = useMedicamentStore(
    (state) => ({
      CreateMedicament: state.CreateMedicament,
      SetStep: state.SetStep,
      search: state.search,
      SetSearch: state.SetSearch,
    })
  );

  useScrollToTop();

  function generateUUID() {
    return "xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx".replace(
      /[xy]/g,
      function (c) {
        var r = (Math.random() * 16) | 0,
          v = c === "x" ? r : (r & 0x3) | 0x8;
        return v.toString(16);
      }
    );
  }

  const submitData = async (data: IMedicament) => {
    CreateMedicament({ ...data, uicodproducto: generateUUID(), new: true });
  };
  const fields: Field[] = [
    {
      label: "Nombre del medicamento *",
      name: "name",
      required: true,
      type: "text",
      default: search || "",
    },
    {
      label: "Posología *",
      name: "indications",
      required: true,
      max: 200,
      type: "textarea",
      default: "",
    },
  ];
  return (
    <section>
      <div className="container-AddMdicine px-10 py-3 mt-5 mb-6">
        <div className="flex flex-col md:flex-row justify-between">
          <p className="text-[#4B4B4B] text-[18px] font-bold mt-3">
            Llene los datos para su medicamento
          </p>
          <button
            onClick={() => {
              SetStep(1);
              SetSearch("");
            }}
            className="button-BlacK flex justify-center items-center p-2 w-[120px] mt-4 "
          >
            <FaTrash size={20} /> <p className="ms-1"> Eliminar</p>{" "}
          </button>
        </div>
        <div className="mt-6 pb-3">
          <FormGenerator
            submitData={submitData}
            fields={fields}
            loading={false}
            renderButton={(handleSubmit) => (
              <div className="flex justify-center w-full  ">
                <button
                  className="button-BlacK disabled:opacity-40 flex justify-center items-center border-black border-2 p-1 text-black rounded-lg w-full mx-3  py-2  my-4"
                  type="submit"
                >
                  <FaPlus color="#fbfbfb" className="me-3" size={20} />
                  Agregar medicamento a la receta
                </button>
              </div>
            )}
          />
        </div>
      </div>
    </section>
  );
}
