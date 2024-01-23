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
  const tabStep = usePacients((state) => state.tabStep);
  const SetTabStep = usePacients((state) => state.SetTabStep);
  const tabs = [
    { label: "Datos personales", Component: InformationPatient },
    { label: "Registrar consultorio", Component: SearchMedicine },
    { label: "Registrar consultorio", Component: ConfirmRecipes },
    { label: "Registrar consultorio", Component: Sign },
    { label: "Registrar consultorio", Component: Send },
  ];

  return (
    <section className=" mx-2  md:mx-8 ">
      <section className="flex  ">
        <Tabs
          customStep={tabStep}
          setCustomStep={SetTabStep}
          tabs={tabs}
          hasHeader={false}
        />
      </section>
    </section>
  );
}
