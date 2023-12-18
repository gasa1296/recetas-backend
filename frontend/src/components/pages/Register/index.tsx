import Tabs from "@/components/Tab";
import React, { useEffect } from "react";
import RegisterOffice from "./RegisterOffice";
import ConfirmAccount from "./ConfirmAccount";
import PersonalData from "./PersonalData";
import ProfesionalData from "./ProfesionalData";
import { useRegisterStore } from "@/store/register";
import RegisterSucces from "./RegisterSucces";

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
        <section className="flex  bg-[#F7F7F7]">
            <Tabs tabs={tabs} />
        </section>
    );
}
