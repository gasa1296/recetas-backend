import DashboardLayout from '@/components/Layouts/DashboardLayout'
import SearchRecipe from '@/components/pages/Dashboard/SearchRecipes'
import React from 'react'

export default function SearchRecipesPages() {
    return (
        <DashboardLayout >
            <SearchRecipe />
        </DashboardLayout>
    )
}
