import { useRouter } from 'next/router';
import React, { useEffect } from 'react'
import { MdOutlineArrowBackIos, MdOutlineSearch } from "react-icons/md";
import { useRegisterStore } from "@/store/register";
import Tabs from "@/components/Tab";
import AddPatients from './TabRecipes/AddPatients';
import RegisterQuery from './TabRecipes/RegisterQuery';
import AddMedications from './TabRecipes/AddMedications';
import ConfirmRecipes from './TabRecipes/ConfirmRecipes';
import Sign from './TabRecipes/Sign';


export default function NewRecipes() {
    const router = useRouter();
    const setClearForms = useRegisterStore((state) => state.setClearForms);
    const tabs = [
        { label: "Agregar paciente", Component: AddPatients },
        { label: "Registrar consulta", Component: RegisterQuery },
        { label: "Agregar medicamentos", Component: AddMedications },
        { label: "Confirmar receta", Component: ConfirmRecipes },
        { label: "Firmar", Component: Sign },

    ];
    useEffect(() => {
        return () => setClearForms();
    }, []);

    return (
        <section className=' px-2 md:px-8 mt-4 ' >
            <div className=' container-dashboard'>
                <button onClick={() => router.push(`/dashboard`)} className='button-BlacK flex justify-center items-center p-2 w-[150px] '><MdOutlineArrowBackIos size={20} /> <p className='ms-1'>  Volver al inicio</p>    </button>
            </div>
            <section className="flex   ">
                <Tabs tabs={tabs} hiddenBack />
            </section>
        </section>
    )
}
