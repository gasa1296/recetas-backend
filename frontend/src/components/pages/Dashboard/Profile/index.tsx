import { useRouter } from 'next/router';
import React, { useEffect } from 'react'
import { MdOutlineArrowBackIos } from "react-icons/md";
import TabsProfile from './TabProfile';
import PersonalInformation from './TabProfile/PersonalInformation';
import { useRegisterStore } from '@/store/register';
import Offices from './TabProfile/Offices';
import ProfessionalDataProfile from './TabProfile/ProfessionalDataProfile';

export default function Profile() {
    const router = useRouter();
    const setClearForms = useRegisterStore((state) => state.setClearForms);
    const tabs = [
        { label: "Datos personales", Component: PersonalInformation },
        { label: "Datos profesionales", Component: ProfessionalDataProfile },
        { label: "Consultorios", Component: Offices },


    ];
    useEffect(() => {
        return () => setClearForms();
    }, []);
    return (
        <section className='px-3 md:px-12  mt-8 ' >
            <div className=' container-dashboard'>
                <button onClick={() => router.push(`/dashboard`)} className='button-BlacK flex justify-center items-center p-2 w-[150px] '><MdOutlineArrowBackIos size={20} /> <p className='ms-1'>  Volver al inicio</p>    </button>
            </div>
            <section className="flex bg-[#fff] mt-5  ">
                <TabsProfile tabs={tabs} hiddenBack />
            </section>
        </section>
    )
}
