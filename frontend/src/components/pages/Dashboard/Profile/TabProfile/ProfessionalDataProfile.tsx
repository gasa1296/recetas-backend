import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { ISpecialization } from "@/types/Store/Register";
import { FaSave } from "react-icons/fa";
import * as yup from "yup";
import React from "react";
import { useSpecializationsStore } from "@/store/specializations";
import useCustomEffect from "@/hooks/useCustomEffect";
import Loading from "@/components/Loading";
import { SpecializationProfileSchema } from "@/utils/ValidationSchema/SpecializationSchema";

export default function ProfessionalDataProfile() {
  const {
    specializations,
    UpdateSpecializations,
    loading,
    loadingUpdate,
    GetSpecializations,
  } = useSpecializationsStore((state) => ({
    loading: state.loading,
    loadingUpdate: state.loadingUpdate,
    specializations: state.specializations,
    UpdateSpecializations: state.UpdateSpecializations,
    GetSpecializations: state.GetSpecializations,
  }));

  const submitData = async (data: { specializations: ISpecialization[] }) => {
    const result = await UpdateSpecializations(data.specializations);
    if (result) GetSpecializations();
  };

  useCustomEffect({ requestGet: GetSpecializations });

  const schema = yup.object().shape({
    specializations: yup
      .array()
      .of(SpecializationProfileSchema)
      .min(1, "Debe tener al menos una especialización")
      .required("Debe tener al menos una especialización"),
  });

  const fields: Field[] = [
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
      default: specializations || [
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
          name: "id",
          required: true,
          type: "invisible",
          subFormKey: "id",
          default: specializations || "",
        },
        {
          label: "Licenciatura *",
          secondLabel: "Especialidad *",
          name: "name",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "name",
          default: specializations || "",
        },
        {
          label: "Cédula profesional *",
          name: "identification",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "identification",
          default: specializations || "",
        },
        {
          label: "Institución que otorga la licenciatura *",
          secondLabel: "Institución que otorga la especialidad *",
          name: "university",
          required: true,
          type: "text",
          width: 50,
          subFormKey: "university",
          default: specializations || "",
        },
        {
          label: "Agrega el logotipo de tu Universidad *",
          name: "logo",
          maxFile: 1,
          required: false,
          type: "file",
          width: 100,
          subFormKey: "logo",
          default: specializations || "",
        },
      ],
    },
  ];

  if (loading) return <Loading />;

  if (!specializations) return <> </>;

  return (
    <section className="  bg-[#fff] mt-5">
      <FormGenerator
        submitData={submitData}
        fields={fields}
        loading={false}
        schema={schema}
        buttonText="Continuar"
        renderButton={(handleSubmit) => (
          <div className="flex justify-center w-full  ">
            <button
              onClick={(e) => {
                e.preventDefault();
                handleSubmit();
              }}
              disabled={loadingUpdate}
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
