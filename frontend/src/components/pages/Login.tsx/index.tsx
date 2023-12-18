import { useRouter } from 'next/router'
import React from 'react'

export default function Login() {
    const router = useRouter()

    return (
        <section className='bg-[#F7F7F7] md:p-10 flex justify-center '>
            <div className=' w-[610px] h-[355px] p-7 bg-[#FFFFFF]  shadow-lg shadow-[#2C27380A] '>
                <h6 className='font-normal  text-[#333333] text-[33px] text-center'>Iniciar sesión</h6>

                <input type="email" placeholder='Correo electrónico* ' className='my-4 w-96 p-2 border border-[ #DBE2EA] h-[52px] text-[#3D3D3D]  mx-auto block rounded focus:outline-none' />
                <input type="password" placeholder='Contraseña* ' className='my-4 w-96 p-2 border border-[ #DBE2EA] h-[52px] text-[#3D3D3D]  mx-auto block rounded focus:outline-none' />

                <button className='border bg-[#000000] text-[#EBF4F8] w-56 p-3 my-4  rounded-lg block mx-auto' onClick={() => router.push(`/`)}>Iniciar sesión</button>
            </div>

        </section>
    )
}
