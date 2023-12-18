import { useAuthStore } from "@/store/auth";
import { Router, useRouter } from "next/router";
import React from "react";
import { IoMdAddCircleOutline } from "react-icons/io";
import { LuClipboardList } from "react-icons/lu";

export default function DashboardHome() {
    const router = useRouter();
    const { user } = useAuthStore((state) => ({
        user: state.user,
    }));

    return (
        <section className='p-10'>

            <p className='text-[ #1A1A1A] text-[24px]'>Bienvenido Dr. {user?.first_name} {user?.last_name1}, desde esta plataforma podrá generar sus recetas electrónicas, también podrá consultar sus recetas generadas</p>
            <section className='block mt-10 md:flex' >
                <div className='flex items-center justify-center card mt- ' onClick={() => router.push(`/dashboard/newRecipes`)}>
                    <IoMdAddCircleOutline size={50} className="me-5" />
                    <div >
                        <p className='card-title ' >Nueva </p>
                        <p className='card-title'>receta</p>
                    </div>

                </div>
                <div className='flex items-center justify-center mx-0 md:mx-8 card mt-8 md:mt-0' onClick={() => router.push(`/dashboard/searchRecipes`)}>
                    <LuClipboardList size={50} className="me-5" />
                    <div >
                        <p className='card-title ' >Buscar </p>
                        <p className='card-title'>receta</p>
                    </div>



                </div>
            </section>
        </section>
    );
}
