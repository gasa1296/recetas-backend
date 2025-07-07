import FormGenerator from "@/components/FormGenerator";
import Loading from "@/components/Loading";
import { useRegisterStore } from "@/store/register";
import { Field } from "@/types/Generals/FormGenerator";
import { IForm1, IRegisterPayload } from "@/types/Store/Register";
import { validateSameObject } from "@/utils/isSameObject";
import * as yup from "yup";
import React from "react";
import toast from "react-hot-toast";
import { form1Schema } from "./helper";
import ModalAutoPopulate from "@/components/FormGenerator/Components/InputSelectSearch/NoOptions/ModalAutoPopulate";
import { autopopulateProfile } from "../../../services/auth";

export default function PersonalData({ nextStep }: any) {
  const [customLoading, setCustomLoading] = React.useState(false);
  const {
    form1,
    idCX,
    loading,
    handleAutoPopulateByName,
    enableSearch,
    setSelectedOption,
    setForm1,
  } = useRegisterStore((state) => ({
    form1: state.form1,
    idCX: state.idCX,
    loading: state.loading,
    enableSearch: state.enableSearch,
    handleAutoPopulateByName: state.handleAutoPopulateByName,
    setSelectedOption: state.setSelectedOption,
    setForm1: state.setForm1,
  }));

  const [showModal, setShowModal] = React.useState(false);
  const [autoPopulateOptions, setAutoPopulateOptions] = React.useState<any[]>(
    []
  );

  const submitData = async (data: IRegisterPayload) => {
    if (data.password !== data.confirmPassword) {
      return toast.error("Las contraseñas no coinciden");
    }

    if (!idCX) {
      setCustomLoading(true);
      const result = await autopopulateProfile(data.email || "");
      setCustomLoading(false);
      if (result.data.contacts.length >= 1) {
        return toast.error(
          "Se encontraron multiples resultados con este correo electrónico, por favor elige otro correo electrónico"
        );
      }
    }

    nextStep();
  };

  const submitDataAutoPopulate = async (data: {
    nombre: string;
    apellidoPat: string;
    apellidoMat: string;
  }) => {
    try {
      const result = await handleAutoPopulateByName(data);
      if (result.length >= 1) {
        const options = result
          .filter((item: any) => item.datosGenerales.tipo === "Médico")
          .map((item: any) => ({
            label: `${item.datosGenerales.nombre} ${item.datosGenerales.apellidoPaterno} ${item.datosGenerales.apellidoMaterno}`,
            value: item,
            clienteEcommerce: item.datosGenerales.clienteEcommerce,
            cedulas:
              item.listaCedula
                ?.filter((cedula: any) => cedula.cedulaProfesional)
                .map((cedula: any) => cedula.cedulaProfesional)
                .join(", ") || "",
            email: item.listaCorreoElectronico
              .filter((email: any) => email.correroElectronico)
              .map((email: any) => email.correroElectronico),
          }));
        setAutoPopulateOptions(options);
        setShowModal(true);
      }
    } catch (error) {
      console.error(error);
    }
  };

  const handleSelectOption = (option: any) => {
    console.log(option.value);
    setSelectedOption(option.value);
  };

  const fieldsAutopulate: Field[] = [
    {
      label: "Nombre",
      name: "nombre",
      type: "text",
      width: 100,
      default: "",
      required: true,
      mayuscula: true,
    },
    {
      label: "Apellido Paterno",
      name: "apellidoPat",
      type: "text",
      width: 50,
      default: "",
      required: true,
      mayuscula: true,
    },
    {
      label: "Apellido Materno",
      name: "apellidoMat",
      type: "text",
      width: 50,
      default: "",
      required: false,
      mayuscula: true,
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
      mayuscula: true,
    },
    {
      label: "Apellido Paterno *",
      name: "last_name1",
      required: true,
      disabled: enableSearch,
      type: "text",
      width: 50,
      default: form1?.last_name1 ?? "",
      mayuscula: true,
    },
    {
      label: "Apellido Materno *",
      name: "last_name2",
      required: true,
      disabled: enableSearch,
      type: "text",
      width: 50,
      default: form1?.last_name2 ?? "",
      mayuscula: true,
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
      label: "Seleccionar Género *",
      name: "gender",
      required: true,
      disabled: enableSearch,
      type: "select",
      options: [
        { label: "Masculino", value: "M" },
        { label: "Femenino", value: "F" },
        { label: "Indefinido", value: "I" },
      ],
      width: 50,
      default: form1?.gender ?? "M",
    },
    {
      label: "Teléfono celular ",
      name: "phone1",
      required: true,
      // disabled: enableSearch,
      type: "multiPhone",
      width: 50,
      default: form1?.phone1 ?? [""],
      maxFile: 10,
    },
    {
      label: "Teléfono fijo (Opcional)",
      name: "phone2",
      required: false,
      type: "text",
      width: 50,
      default: form1?.phone2 ?? "",
      maxFile: 10,
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
      minLength: 8,
    },
    {
      label: "Confirmar contraseña *",
      name: "confirmPassword",
      required: true,
      type: "password",
      width: 50,
      default: form1?.confirmPassword ?? "",
      minLength: 8,
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
        Ingrese su nombre y apellidos para buscar su información
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
        schema={form1Schema}
        submitData={submitData}
        fields={fields}
        loading={customLoading}
        buttonText="Continuar"
      />

      <ModalAutoPopulate
        show={showModal}
        closeModal={() => setShowModal(false)}
        options={autoPopulateOptions}
        onSelect={handleSelectOption}
      />
    </section>
  );
}
