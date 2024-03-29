import FormGenerator from "@/components/FormGenerator";
import Loading from "@/components/Loading";
import { useRegisterStore } from "@/store/register";
import { Field } from "@/types/Generals/FormGenerator";
import { IForm1, IRegisterPayload } from "@/types/Store/Register";
import { validateSameObject } from "@/utils/isSameObject";
import React from "react";
import toast from "react-hot-toast";

export default function PersonalData({ nextStep }: any) {
  const setForm1 = useRegisterStore((state) => state.setForm1);
  const { form1, loading, handleAutoPopulate, enableSearch } = useRegisterStore(
    (state) => ({
      form1: state.form1,
      loading: state.loading,
      enableSearch: state.enableSearch,
      handleAutoPopulate: state.handleAutoPopulate,
    })
  );

  const submitData = async (data: IRegisterPayload) => {
    if (data.password !== data.confirmPassword) {
      return toast.error("Las contraseñas no coinciden");
    }

    nextStep();
  };
  const submitDataAutoPopulate = async (data: { search: string }) => {
    handleAutoPopulate(data.search);
  };

  const fieldsAutopulate: Field[] = [
    {
      label: "Buscate por email o cedula *",
      name: "search",
      required: true,
      type: "text",
      width: 100,
      default: "",
    },
  ];
  const fields: Field[] = [
    {
      label: "Nombre(s) *",
      name: "first_name",
      required: true,
      disabled: enableSearch,
      type: "text",
      width: 50,
      default: form1?.first_name ?? "",
    },
    {
      label: "Apellido Paterno *",
      name: "last_name1",
      required: true,
      disabled: enableSearch,
      type: "text",
      width: 50,
      default: form1?.last_name1 ?? "",
    },
    {
      label: "Apellido Materno *",
      name: "last_name2",
      required: true,
      disabled: enableSearch,
      type: "text",
      width: 50,
      default: form1?.last_name2 ?? "",
    },

    {
      label: "Correo electrónico *",
      name: "email",
      required: true,
      disabled: enableSearch,
      type: "email",
      width: 50,
      default: form1?.email ?? "",
    },

    {
      label: "Teléfono celular *",
      name: "phone1",
      required: true,
      disabled: enableSearch,
      type: "text",
      width: 50,
      default: form1?.phone1 ?? "",
      maxFile: 10,
    },
    {
      label: "Teléfono fijo",
      name: "phone2",
      required: false,
      type: "text",
      width: 50,
      default: form1?.phone2 ?? "",
      maxFile: 10,
    },
    {
      label: "Seleccionar Género *",
      name: "gender",
      required: true,
      disabled: enableSearch,
      type: "select",
      options: [
        { label: "Masculino", value: "0" },
        { label: "Femenino", value: "1" },
        { label: "Indefinido", value: "2" },
      ],
      width: 50,
      default: form1?.gender ?? "",
    },
    {
      label: "Código FESA *",
      name: "fesa",
      required: true,
      type: "text",
      width: 50,
      default: form1?.fesa ?? "",
    },
    {
      label: "Contraseña *",
      name: "password",
      required: true,
      type: "password",
      width: 50,
      default: form1?.password ?? "",
    },
    {
      label: "Confirmar contraseña *",
      name: "confirmPassword",
      required: true,
      type: "password",
      width: 50,
      default: form1?.confirmPassword ?? "",
    },
  ];

  if (loading)
    return (
      <section className="max-w-[1000px] mx-auto px-3 md:px-2">
        <Loading />
      </section>
    );

  return (
    <section className="max-w-[1000px] mx-auto px-3 md:px-2">
      <h2 className="text-center text-[#1A1A1A] text-[24px] mt-5 font-medium">
        Buscate para autorellenar tus datos
      </h2>

      <FormGenerator
        submitData={submitDataAutoPopulate}
        fields={fieldsAutopulate}
        loading={false}
        buttonText="Buscar"
      />

      <h2 className="text-center text-[#1A1A1A] text-[24px] mt-5 font-medium">
        Ingrese su información personal para registrarse
      </h2>
      <h4 className="px-2 font-bold text-[#4B4B4B] text-[20px] text-start my-7">
        Datos personales
      </h4>

      <FormGenerator
        onFormChange={(form) => {
          if (validateSameObject(form1 as object, form)) {
            setForm1(form as IForm1);
          }
        }}
        submitData={submitData}
        fields={fields}
        loading={false}
        buttonText="Continuar"
      />
    </section>
  );
}
