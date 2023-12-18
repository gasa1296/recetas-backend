import React from 'react'
import Footer from '../AuthLayout/Footer'
import NavRegister from './Nav'
interface Props { children: React.ReactNode }

export default function RegisterLayout({ children }: Props) {
    return (
        <div>
            <NavRegister />
            {children}
            <Footer />
        </div>
    )
}
