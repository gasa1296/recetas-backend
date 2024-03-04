import React, { useState } from "react";
import Image from "next/image";
import medicineLogo from "@/assets/images/medicine.png";
import { FaPlus, FaTrash } from "react-icons/fa";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
export default function ModalEditMedicine({
  show,
  closeModal,
  updateMedicament,
}: any) {
  const submitData = async (data: any) => {
    updateMedicament(show.uuid, data);
    closeModal();
  };
  const fields: Field[] = show.new
    ? [
        {
          label: "Nombre del medicamento *",
          name: "name",
          required: true,
          type: "text",
          default: show.name,
        },
        {
          label: "Indicaciones adicionales *",
          name: "indications",
          required: true,
          type: "textarea",
          default: show.indications,
        },
      ]
    : [
        {
          label: "Dosis *",
          name: "dose",
          required: true,
          type: "text",
          width: 50,
          default: show.dose,
        },
        {
          label: "Frecuencia *",
          name: "frequency",
          required: true,
          type: "text",
          width: 50,
          default: show.frequency,
        },
        {
          label: "Duración *",
          name: "duration",
          required: true,
          type: "text",
          width: 50,
          default: show.duration,
        },
        {
          label: "Via de administración *",
          name: "way",
          required: true,
          type: "text",
          width: 50,
          default: show.way,
        },
        {
          label: "Indicaciones adicionales",
          name: "add",
          required: false,
          type: "text",
          width: 50,
          default: show.add,
        },
        {
          label: "Cantidad de cajas para cubrir el tratamiento *",
          name: "quantity",
          required: true,
          type: "number",
          width: 50,
          default: show.quantity,
        },
      ];

  return (
    <>
      {show && (
        <div className="fixed inset-0 bg-gray-800 bg-opacity-75 transition-opacity">
          <div className="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div className="flex min-h-full items-end justify-center p-2 text-center sm:items-center sm:p-0">
              <div className="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                <div className="flex justify-between px-3 py-3 ">
                  <p className="text-[#4B4B4B] font-bold text-[20px]">Editar</p>
                  <p
                    onClick={(e) => {
                      e.preventDefault();
                      closeModal();
                    }}
                    className="text-[#4B4B4B] font-bold text-[18px] cursor-pointer"
                  >
                    Cerrar
                  </p>
                </div>
                <div className="bar-lateral"> </div>

                <div className="bg-white px-2 pb-4 pt-5 sm:p-2 sm:pb-4">
                  <div className="flex justify-between items-center ">
                    <div className=" mt-2 flex justify-center items-center ">
                      <div className="border-stone-950">
                        <p className="text-[#1A1A1A] font-bold text-[24px] ms-4 mt-2">
                          {show.name}
                        </p>
                        <p className="text-[#141414] text-[20px] ms-4 mt-1">
                          {show.new
                            ? show.indications
                            : `${show.dose} | ${show.frequency} | ${show.duration} | ${show.via} | ${show.indications} | ${show.quantity}`}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div className="mt-4 px-2">
                    <FormGenerator
                      submitData={submitData}
                      fields={fields}
                      loading={false}
                      buttonText="Continuar"
                      renderButton={(handleSubmit) => (
                        <div className=" w-full  ">
                          <div className="bar-lateral  " />

                          <div className=" px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button
                              onClick={(e) => {
                                e.preventDefault();
                                handleSubmit(submitData);
                              }}
                              type="button"
                              className="inline-flex w-full justify-center rounded-md px-3 py-2 button-green text-sm font-semibold text-white shadow-sm hover:bg-gray-50 sm:ml-3 sm:w-auto"
                            >
                              Guardar
                            </button>
                            <button
                              type="button"
                              className="mt-3 inline-flex w-full justify-center rounded-md button-edit px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50  sm:mt-0 sm:w-auto"
                              onClick={(e) => {
                                e.preventDefault();
                                closeModal();
                              }}
                            >
                              Cancelar
                            </button>
                          </div>
                        </div>
                      )}
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
