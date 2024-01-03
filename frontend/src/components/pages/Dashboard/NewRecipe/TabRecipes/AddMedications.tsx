import React from 'react'
import { FaPills } from "react-icons/fa";


export default function AddMedications({ nextStep, backStep }: any) {
    return (
        <section className='px-12 p-2 bg-[#fff] mt-5'>
            <div className='px-12 p-2 bg-[#fff] mt-3 '>
                <p className='text-[18px] text-[#1A1A1A] font-bold my-4'>Buscar Medicamentos</p>
                <input type="search" placeholder=' Buscar receta por Nombre de paciente, Correo, Teléfono, Folio, Fecha' className='focus:outline-none p-3 input-search' />
                <div className='bar-search'></div>
                <p className='text-[18px] text-[#4B4B4B] font-bold my-4'>Medicamentos agregados en su receta</p>
                <p className='text-[#A0A0A0] text-[18px] mb-4'>Revise los medicamentos que ha agregado a su receta.</p>
                <div className='flex  justify-center items-center container-box pb-5'>
                    <FaPills size={60} />
                    <p className='text-[28px] font-normal text-[#1A1A1A]  ms-4 w-[590px] '>Antes de generar la receta, busque los medicamentos que requiera su paciente.</p>
                </div>
                <div className='block md:flex justify-center mt-16 p-6'>
                    <button onClick={() => { backStep() }} className='button-white w-[214px] p-2 mx-3'>Regresar</button>
                    <button onClick={() => { nextStep() }} className='button-BlacK w-[214px] p-2 text-[20px] mx-3'>Continuar</button>
                </div>

            </div>
        </section>
    )
}
