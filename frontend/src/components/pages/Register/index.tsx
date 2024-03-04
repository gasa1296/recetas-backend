import Tabs from "@/components/Tab";
import React, { useEffect } from "react";
import RegisterOffice from "./RegisterOffice";
import ConfirmAccount from "./ConfirmAccount";
import PersonalData from "./PersonalData";
import ProfesionalData from "./ProfesionalData";
import { useRegisterStore } from "@/store/register";
import RegisterSucces from "./RegisterSucces";
import { FaRegUser } from "react-icons/fa";

export default function Register() {
  const setClearForms = useRegisterStore((state) => state.setClearForms);
  const tabs = [
    { label: "Datos personales", Component: PersonalData },
    { label: "Datos profesionales", Component: ProfesionalData },
    { label: "Registrar consultorio", Component: RegisterOffice },
    { label: "Confirmar cuenta", Component: ConfirmAccount },
    {
      label: "Gracias por registrarse",
      Component: RegisterSucces,
      activeDefaultTab: true,
    },
  ];

  useEffect(() => {
    return () => setClearForms();
  }, []);

  return (
    <section className=" p-5 bg-[#F7F7F7]">
      <div className="flex items-center  border-Tab p-2 ps-3 ">
        <FaRegUser color="#Fff " size={28} />
        <p className="text-[#fff] text-[26px] ms-3">Registro médico</p>
      </div>
      <section className="container-Patiens   flex justify-center  ">
        <Tabs disableMargin={true} tabs={tabs} />
      </section>
    </section>
  );
}
