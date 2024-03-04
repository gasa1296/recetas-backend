import React from "react";
import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { FaSave } from "react-icons/fa";
import { useAuthStore } from "@/store/auth";
import { IUser } from "@/types/Models/User";

export default function PersonalInformation({ nextStep, backStep }: any) {
  const { user, UpdateProfile, loading } = useAuthStore((state) => ({
    loading: state.loading,
    user: state.user,
    UpdateProfile: state.UpdateProfile,
  }));

  const submitData = async (data: IUser) => {
    UpdateProfile(data as any);
  };

  const fields: Field[] = [
    {
      label: "Nombre(s) *",
      name: "first_name",
      required: true,
      type: "text",
      width: 50,
      default: user?.first_name || "",
    },
    {
      label: "Apellido Paterno *",
      name: "last_name1",
      required: true,
      type: "text",
      width: 50,
      default: user?.last_name1 || "",
    },
    {
      label: "Apellido Materno *",
      name: "last_name2",
      required: true,
      type: "text",
      width: 50,
      default: user?.last_name2 || "",
    },

    {
      label: "Correo electrónico *",
      name: "email",
      required: true,
      disabled: true,
      type: "email",
      width: 50,
      default: user?.email || "",
    },

    {
      label: "Teléfono celular *",
      name: "phone1",
      required: true,
      type: "text",
      width: 50,
      default: user?.phone1 || "",
      maxFile: 10,
    },
    {
      label: "Teléfono fijo",
      name: "phone2",
      required: false,
      type: "text",
      width: 50,
      default: user?.phone2 || "",
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
      width: 50,
      default: user?.gender || "",
    },
    {
      label: "Código FESA *",
      name: "fesa",
      required: true,
      type: "number",
      width: 50,
      default: user?.fesa || "",
    },
  ];
  return (
    <section className=" bg-[#fff] mt-5 ">
      <FormGenerator
        submitData={submitData}
        fields={fields}
        loading={false}
        renderButton={(handleSubmit) => (
          <div className="flex justify-center w-full  ">
            <button
              onClick={(e) => {
                e.preventDefault();
                handleSubmit();
              }}
              disabled={loading}
              className="button-BlacK disabled:opacity-40 font-bold md:flex justify-center items-center border-black border-2 p-3 text-black rounded-lg w-60 mx-3 block my-4"
              type="submit"
            >
              <div className="flex justify-center items-center">
                <FaSave color="#fbfbfb" className="me-3" size={20} />
                Guardar
              </div>
            </button>
          </div>
        )}
      />
    </section>
  );
}
