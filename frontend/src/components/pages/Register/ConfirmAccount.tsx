import FormGenerator from "@/components/FormGenerator";
import { useRegisterStore } from "@/store/register";
import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

import { IRegisterPayload } from "@/types/Store/Register";
import MexicoStates from "@/utils/constants/mexico-states.json";
import useScrollToTop from "@/hooks/useScrollToTop";
import LoadingModal from "@/components/Loading/LoadingModal";
import { useRoomsStore } from "@/store/rooms";
import { confirmationSchema } from "./helper";
import { useSpecializationsStore } from "@/store/specializations";
import useCustomEffect from "@/hooks/useCustomEffect";
import ModalUniversityNotFound from "@/components/FormGenerator/Components/InputSelectSearch/NoOptions/ModalUniversityNotFound";
import UniversityNotFound from "@/components/FormGenerator/Components/InputSelectSearch/NoOptions/UniversityNotFound";

export default function ConfirmAccount({ nextStep, backStep }: any) {
  const { form3, form2, form1, handleSubmit, loading, enableSearch } =
    useRegisterStore((state) => ({
      form3: state.form3,
      form2: state.form2,
      form1: state.form1,
      handleSubmit: state.handleSubmit,
      loading: state.loading,
      enableSearch: state.enableSearch,
    }));

  const roomDesigns = useRoomsStore((state) => state.roomDesigns);

  const { university, GetUniversity } = useSpecializationsStore((state) => ({
    university: state.university,
    GetUniversity: state.GetUniversity,
  }));

  useScrollToTop();
  useCustomEffect({ requestGet: GetUniversity });

  const universityOptions = university?.map((item) => ({
    label: item.name,
    value: item.name,
  }));

  const submitData = async (data: IRegisterPayload) => {
    const result = await handleSubmit(data);

    if (result) nextStep();
  };

  let phone1Parse;
  try {
    phone1Parse =
      typeof form1?.phone1 === "string"
        ? JSON.parse(form1?.phone1 || "")
        : form1?.phone1 || [""];
  } catch (error) {
    phone1Parse = [""];
  }

  const fields: Field[] = [
    {
      label: "Nombre(s) *",
      name: "first_name",
      required: true,
      disabled: enableSearch,
      type: "text",
      width: 50,
      default: form1?.first_name || "",
    },
    {
      label: "Apellido Paterno *",
      name: "last_name1",
      required: true,
      disabled: enableSearch,
      type: "text",
      width: 50,
      default: form1?.last_name1 || "",
    },
    {
      label: "Apellido Materno *",
      name: "last_name2",
      disabled: enableSearch,
      required: true,
      type: "text",
      width: 50,
      default: form1?.last_name2 || "",
    },

    {
      label: "Correo electrónico *",
      name: "email",
      required: true,
      disabled: enableSearch,
      type: "email",
      width: 50,
      default: form1?.email || "",
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
      default: form1?.gender || "",
    },
    {
      label: "Teléfono celular ",
      name: "phone1",
      //disabled: enableSearch,
      required: true,
      type: "multiPhone",
      width: 50,
      default: phone1Parse,
      maxFile: 10,
    },
    {
      label: "Teléfono fijo (Opcional)",
      name: "phone2",
      required: false,
      type: "text",
      width: 50,
      default: form1?.phone2 || "",
      maxFile: 10,
    },

    {
      label: "Código FESA *",
      name: "fesa",
      required: true,
      type: "text",
      width: 50,
      default: form1?.fesa || "",
    },
    {
      label: "Contraseña *",
      name: "password",
      required: true,
      type: "password",
      width: 50,
      default: form1?.password || "",
    },
    {
      label: "Confirmar contraseña *",
      name: "confirmPassword",
      required: true,
      type: "password",
      width: 50,
      default: form1?.confirmPassword || "",
    },

    {
      label: "Datos profesionales",
      name: "title",
      required: true,
      type: "title",
      width: 100,
    },
    {
      label: "",
      name: "specializations",
      type: "subForm",
      buttonAddText: "Agregar especialidad",
      maxFile: 1,
      width: 100,
      default: form2?.specializations || [
        {
          name: "",
          identification: "",
          university: "",
          file: null,
        },
      ],
      form: [
        {
          label: "Datos licenciatura",
          secondLabel: "Datos especialidad",
          name: "title",
          required: true,
          type: "title",
          width: 100,
        },
        {
          label: "Licenciatura *",
          secondLabel: "Especialidad *",
          name: "name",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "name",
          default: form2?.specializations || "",
        },
        {
          label: "Cédula profesional *",
          name: "identification",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "identification",
          default: form2?.specializations || "",
        },
        {
          label: "Institución que otorga la licenciatura *",
          secondLabel: "Institución que otorga la especialidad *",
          name: "university",
          ModalNotFound: ModalUniversityNotFound,
          NotFound: UniversityNotFound,
          required: true,
          type: "selectSearch",
          options: universityOptions,
          width: 50,
          subFormKey: "university",
          default: form2?.specializations || "",
          customChange: async (newValue: any, setValue: any) => {
            const findUniversity = university?.find(
              (uni) => uni.name === newValue
            );

            if (findUniversity?.image) {
              setValue("file", [findUniversity?.image || ""]);
              setValue("temporalLogo", [findUniversity?.image || ""]);
            }
          },
        },
        {
          label: "Agrega el logotipo de tu Universidad *",
          name: "file",
          temporalName: "temporalLogo",
          maxFile: 1,
          required: false,
          type: "file",
          width: 100,
          subFormKey: "file",
          default: form2?.specializations || "",
        },
      ],
    },

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
      default: form3?.rooms || [
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
          default: form3?.rooms || "",
        },
        {
          label: "Código Postal *",
          name: "zip",
          required: true,
          type: "number",
          width: 50,
          subFormKey: "zip",
          default: form3?.rooms || "",
        },
        {
          label: "Calle *",
          name: "street",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "street",
          default: form3?.rooms || "",
        },
        {
          label: "Colonia *",
          name: "colony",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "colony",
          default: form3?.rooms || "",
        },
        {
          label: "Estado *",
          name: "state",
          required: true,
          type: "selectSearch",
          width: 50,
          options: MexicoStates,
          subFormKey: "state",
          default: form3?.rooms || "",
        },
        {
          label: "Delegación o Municipio *",
          name: "delegation",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "delegation",
          default: form3?.rooms || "",
        },
        {
          label: "Número exterior *",
          name: "n_exterior",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "n_exterior",
          default: form3?.rooms || "",
        },
        {
          label: "Número interior (Opcional)",
          name: "n_interior",
          required: false,
          type: "text",
          width: 50,
          subFormKey: "n_interior",
          default: form3?.rooms || "",
        },
        {
          label: "Piso / Nº de local / No. Consultorio (Opcional)",
          name: "address",
          required: false,
          type: "text",
          width: 50,
          subFormKey: "address",
          default: form3?.rooms || "",
        },
        {
          label: "Teléfono de consultorio (Opcional)",
          name: "phone",
          required: false,
          type: "text",
          width: 50,
          subFormKey: "phone",
          default: form3?.rooms || "",
        },

        {
          label: "Agrega el logotipo de su consultorio ",
          name: "files",
          required: false,
          type: "file",
          width: 100,
          subFormKey: "files",
          default: form3?.rooms || "",
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
          default: form3?.rooms || "",
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
      {loading && <LoadingModal />}
      <h2 className="text-center text-[#1A1A1A] text-[28px] mt-5 font-medium">
        Confirme la información de su cuenta
      </h2>
      <h4 className="font-bold text-[#4B4B4B] text-[24px] text-start my-7">
        Datos personales
      </h4>
      <div className="flex   ">
        <div className=" w-full">
          <FormGenerator
            buttonText="Continuar"
            submitData={submitData}
            fields={fields}
            loading={false}
            schema={confirmationSchema}
            renderButton={(handleSubmit) => (
              <div className="flex justify-center w-full ">
                <button
                  onClick={(e) => {
                    e.preventDefault();
                    backStep();
                  }}
                  disabled={loading}
                  className="bg-white disabled:opacity-40 font-bold border-black border-2 p-3 text-black rounded-lg w-60 mx-3 block my-4"
                  type="submit"
                >
                  Anterior
                </button>
                <button
                  onClick={(e) => {
                    e.preventDefault();
                    handleSubmit();
                  }}
                  disabled={loading}
                  className="bg-[#000000] p-3 disabled:opacity-40 text-[#EBF4F8] rounded-lg w-60 mx-3 block my-4"
                  type="submit"
                >
                  Registrarse
                </button>
              </div>
            )}
          />
        </div>
      </div>
    </section>
  );
}
