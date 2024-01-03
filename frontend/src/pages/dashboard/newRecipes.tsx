import DashboardLayout from '@/components/Layouts/DashboardLayout'
import NewRecipes from '@/components/pages/Dashboard/NewRecipe'
import React from 'react'

export default function NewRecipesPages() {
    return (
        <DashboardLayout >
            <NewRecipes />
        </DashboardLayout>
    )
}
