import AuthLayout from '@/components/Layouts/AuthLayout'
import ForgortPassword from '@/components/pages/ForgotPasword'
import React from 'react'

export default function forgotPassword() {
    return (
        <main
        >
            <AuthLayout >
                <ForgortPassword />
            </AuthLayout>
        </main >
    )
}
