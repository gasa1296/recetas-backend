import { useRegisterStore } from '@/store/register';
import React, { useEffect } from 'react'
import Tabs from "@/components/Tab";
import SearchPatiens from './TabPatiens/SearchPatiens';

export default function Patients() {
    const setClearForms = useRegisterStore((state) => state.setClearForms);

    const tabs = [
        { label: "Datos personales", Component: SearchPatiens },
        /*        { label: "Datos profesionales", Component: ProfesionalData },
               { label: "Registrar consultorio", Component: RegisterOffice },
               { label: "Confirmar cuenta", Component: ConfirmAccount }, */

    ];

    useEffect(() => {
        return () => setClearForms();
    }, []);
    return (
        <section className=' mt-8 mx-8 '>
            <section className="flex  ">
                <Tabs tabs={tabs} hasHeader={false} />
            </section>
        </section>
    )
}
