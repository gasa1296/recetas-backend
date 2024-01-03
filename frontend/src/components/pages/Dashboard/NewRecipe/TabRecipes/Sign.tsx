import React from 'react'

export default function Sign({ nextStep, backStep }: any) {
    return (
        <section className="px-12 p-3 mt-8  bg-[#FFFFFF]  ">
            <h6 className='text-[24px] text-[#1A1A1A] font-bold my-4 '>Datos de la receta</h6>
            <section className='flex justify-between'>
                <div className=' w-[30%]'>
                    <p className='title-input'>Fecha</p>
                    <input type='text' className=' w-full input-sing focus:outline-none' placeholder="24 de Marzo 2023" />

                </div>
                <div className='w-[30%]'>
                    <p className='title-input'>Folio</p>
                    <input type='text' className=' w-full input-sing focus:outline-none' placeholder='1289' />
                </div>

            </section>

            <section className='flex justify-between my-5'>
                <div className='w-[49%]'>
                    <p className='title-input'>Nombre</p>
                    <input type='text' className='w-full input-sing focus:outline-none' placeholder='Victor Hernández Rodríguez' />
                </div>
                <div className='w-[49%]'>
                    <p className='title-input'>Correo electrónico</p>
                    <input type='text' className='w-full input-sing focus:outline-none' placeholder='victor.hernandez@fanafesa.com' />

                </div>


            </section>
            <div className='bar-search w-full my-6 '></div>


            <h6 className='text-[24px] text-[#1A1A1A] font-bold my-4 '>  Selecciona el consultorio para generar tu receta electrónica</h6>
            <section className='flex  justify-around'>
                <div className='flex card-sing p-2'>
                    <input type="checkbox" className="default:ring-1 mx-2 " />
                    <div className='ms-4'>
                        <h5 className='title-card'>Hospital Angeles Lindavista</h5>
                        <p className='text-card'>Riobamba 639 Col, Magdalena de las Salinas</p>
                        <p className='text-card' >Gustavo A. Madero, 07760</p>
                        <p className='text-card'>Ciudad de México, CDMX</p>
                    </div>
                </div>
                <div className='flex card-sing p-2'>
                    <input type="checkbox" className="default:ring-1  mx-2" />
                    <div className='ms-5'>
                        <h5 className='title-card'>Hospital Angeles Roma</h5>
                        <p className='text-card'>Querétaro 58, Roma Nte</p>
                        <p className='text-card' >Cuauhtémoc, 06700</p>
                        <p className='text-card'>Ciudad de México, CDMX</p>
                    </div>
                </div>

            </section>
            <div className='bar-search w-full my-6 '></div>
            <h6 className='text-[24px] text-[#1A1A1A] font-bold my-4 text-center'>Firma tu receta en el recuadro o agrégala desde un archivo como imagen</h6>
            <div className='block md:flex justify-center mt-16 p-6'>
                <button onClick={() => { backStep() }} className='button-white w-[214px] p-2 mx-3'>Regresar</button>
                <button onClick={() => { nextStep() }} className='button-BlacK w-[214px] p-2 text-[20px] mx-3'>Crear receta</button>
            </div>
        </section >
    )
}
