import React from 'react'
import { MdOutlineArrowBackIos, MdOutlineSearch } from "react-icons/md";
import { FaFilePrescription } from "react-icons/fa";
import { useRouter } from 'next/router';

export default function SearchRecipe() {
    const router = useRouter();

    return (
        <section className=' px-2 md:px-12 p-3 mt-8'>
            <div className=' container-dashboard'>

                <button onClick={() => router.push(`/dashboard`)} className='button-BlacK flex justify-center items-center p-2 w-[150px] '><MdOutlineArrowBackIos size={20} /> <p className='ms-1'>  Volver al inicio</p>    </button>
            </div>
            <div className='px-12 p-2 bg-[#fff] mt-3 '>
                <p className='text-[18px] text-[#1A1A1A] font-bold my-4'>Buscar recetas generadas</p>
                <input type="search" placeholder=' Buscar receta por Nombre de paciente, Correo, Teléfono, Folio, Fecha' className='focus:outline-none p-3 input-search' />
                <div className='bar-search w-[80%]'></div>
                <p className='text-[18px] text-[#4B4B4B] font-bold my-4'>Información de su paciente</p>
                <div className='flex  justify-center items-center container-box'>
                    <FaFilePrescription size={60} />
                    <p className=' text-[16px] md:text-[28px] font-normal text-[#1A1A1A]  ms-4 w-full md:w-[590px] '>Para iniciar busque una receta por paciente para Ver el historial de recetas generadas</p>
                </div>
            </div>
        </section>
    )
}
