import React, { useEffect } from "react";
import Tabs from "@/components/Tab";
import SearchMedicine from "./TabMedicine/SearchMedicine";

import InformationPatient from "./TabPatiens/InformationPatient";
import { usePacients } from "@/store/pacients";
import ConfirmRecipes from "./ConfirmRecipes";
import Sign from "./Sign";
import Send from "./Send";
import { useMedicamentStore } from "@/store/medicaments";

export default function Patients() {
    const ResetPacients = usePacients((state) => state.ResetPacients);
    const ResetMedicaments = useMedicamentStore(
        (state) => state.ResetMedicaments
    );

    const tabs = [
        { label: "Datos personales", Component: InformationPatient },
        { label: "Registrar consultorio", Component: SearchMedicine },
        { label: "Registrar consultorio", Component: ConfirmRecipes },
        { label: "Registrar consultorio", Component: Sign },
        { label: "Registrar consultorio", Component: Send },
    ];

    useEffect(() => {
        return () => {
            ResetPacients();
            ResetMedicaments();
        };
    }, []);

    return (
        <section className=" mx-2  md:mx-8 ">
            <section className="flex  ">
                <Tabs tabs={tabs} hasHeader={false} />
            </section>
        </section>
    );
}
