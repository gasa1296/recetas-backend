import React from 'react'
import { FaBars } from "react-icons/fa";

import Image from 'next/image'
import logo from '../../../assets/LogoFESA.svg'
import { useRouter } from 'next/router';
import { useAuthStore } from '@/store/auth';
import { RxExit } from "react-icons/rx";
import { getDateFormat } from '@/utils/getDateFormat';
import { FaFilePrescription } from "react-icons/fa";

export default function Nav({ screen, setScreen }: any) {
    const router = useRouter();
    const { user, Logout } = useAuthStore((state) => ({
        user: state.user,
        Logout: state.Logout,
    }));
    const { formattedTime, correctedDate } = getDateFormat("");

    return (
        <nav className=' p-5 block md:flex justify-between  bg-[#FFFFFF] container-nav  relative  '>
            <div className=' absolute top-6 right-14 md:hidden' onClick={() => { setScreen(true) }}>
                <FaBars size={40} className=" absolute " />

            </div>

            <section className=' justify-between items-center  md:flex '>
                <Image src={logo} alt='logo' className='w-[194px] me-9 ' />
                <div className='mx-6 lg:mx-4  hidden md:justify-center items-center md:flex '>
                    <div>
                        <p className='text-[#1A1A1A] text-[24px] font-normal m-0'>Bienvenido</p>
                        <p className='text-[#1A1A1A] text-[20px] font-bold '>Dr. {user?.first_name} {user?.last_name1}</p>
                    </div>

                    <div className='mx-6 lg:mx-14'>
                        <p className="text-[#1A1A1A] text-[24px] font-bold">
                            {formattedTime}
                        </p>
                        <p className="text-[#1A1A1A] text-[18px] font-normal">
                            {correctedDate}
                        </p>
                    </div>
                </div>
            </section>
            <section className=' justify-between items-center  hidden md:flex '>
                <div
                    className="flex items-center mx-4"
                    onClick={async () => {
                        await Logout();
                        router.push(`/`);
                    }}
                >
                    <FaFilePrescription size={50} color="bg-[#fff]" />
                </div>
            </section>
        </nav>
    );
}
