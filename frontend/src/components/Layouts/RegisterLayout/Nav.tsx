import React from 'react'
import Image from 'next/image'
import logo from '../../../assets/LogoFESA.svg'
export default function NavRegister() {
    return (
        <nav className='p-5 ms-16'>
            <Image src={logo} alt='logo' className='w-[194px]' />
        </nav>
    )
}
