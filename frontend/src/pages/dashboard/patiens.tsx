import DashboardLayout from '@/components/Layouts/DashboardLayout'
import Patients from '@/components/pages/Dashboard/Patients'
import React from 'react'

export default function PatiensPage() {
    return (
        <DashboardLayout >
            <Patients />
        </DashboardLayout>
    )
}
