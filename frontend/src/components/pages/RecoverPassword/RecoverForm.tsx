import { useAuthStore } from "@/store/auth";
import { IRecoverPayload } from "@/types/Store/Register";
import { useRouter } from "next/router";
import React from "react";

import { Field } from "@/types/Generals/FormGenerator";
import toast from "react-hot-toast";
import FormGenerator from "@/components/FormGenerator";

export default function RecoverForm({ setSuccess }: { setSuccess: any }) {
  const router = useRouter();
  const { RecoverPassword, loading } = useAuthStore((state) => ({
    RecoverPassword: state.RecoverPassword,
    loading: state.loading,
  }));

  const submitData = async (data: IRecoverPayload) => {
    if (data.password !== data.password_confirmation) {
      return toast.error("Las contraseñas no coinciden");
    }

    const result = await RecoverPassword(data);
    if (result) router.push("/");
  };

  const fields: Field[] = [
    {
      label: "Contraseña *",
      name: "password",
      required: true,
      type: "password",
      width: 100,
      minLength: 8,
    },
    {
      label: "Confirmar contraseña *",
      name: "password_confirmation",
      required: true,
      type: "password",
      width: 100,
      minLength: 8,
    },
  ];

  return (
    <section className=" pt-9 flex justify-center items-center flex-col">
      <div className="w-full max-w-[380px]">
        <FormGenerator
          submitData={submitData}
          fields={fields}
          loading={loading}
          buttonText="Cambiar contraseña"
        />
      </div>
    </section>
  );
}
