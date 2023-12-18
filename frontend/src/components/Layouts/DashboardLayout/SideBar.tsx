import React, { useState } from "react";
import { FaBars, FaRegFileAlt, FaRegUser } from "react-icons/fa";
import { LuArrowLeftToLine } from "react-icons/lu";
import { IoHomeOutline } from "react-icons/io5";
import { RxExit } from "react-icons/rx";
import { TbFileSearch } from "react-icons/tb";
import Image from "next/image";
import { useRouter } from "next/router";
export default function SideBar({ screen, setScreen }: any) {
    const router = useRouter();

    return (
        <div className="main-wrapper  ">
            <nav
                className={`sidebar ${screen ? "show " : ""}  `}
                data-trigger="scrollbar"
            >
                <div
                    className={`overlay sidebar-body  ${screen ? "show" : ""}`}
                >
                    <div
                        onClick={() => {
                            setScreen(false);
                        }}
                        className="flex justify-end cursor-pointer"
                    >
                        <LuArrowLeftToLine size={30} className="m-2" />
                    </div>
                    <section className=" justify-between items-center block md:flex ">
                        <div className="mx-14">
                            <p className="text-[#1A1A1A] text-[24px] font-normal">
                                Bienvenido
                            </p>
                            <p className="text-[#1A1A1A] text-[18px] font-bold">
                                Dr. Victor Hernandez
                            </p>
                        </div>
                    </section>
                    <section className=" justify-between items-center block md:flex ">
                        <div className="mx-14">
                            <p className="text-[#1A1A1A] text-[24px] font-bold">
                                17:43
                            </p>
                            <p className="text-[#1A1A1A] text-[18px] font-normal">
                                Jue 16 de nov
                            </p>
                        </div>
                    </section>
                    <ul className="nav mt-3 ">
                        <li
                            onClick={() => router.push(`/dashboard/patiens`)}
                            className={`${
                                router.pathname === "/dashboard/"
                                    ? "borderTrue"
                                    : ""
                            } nav-category flex items-center   title-li p-2`}
                        >
                            <IoHomeOutline size={18} className="ms-2" />{" "}
                            <p className="ms-2"> Paciente</p>{" "}
                        </li>
                        {/*         <li onClick={() => router.push(`/dashboard/newRecipes`)} className={`${router.pathname === "/dashboard/newRecipes" ? "borderTrue" : ""}  nav-category flex items-center   title-li p-2`}>< FaRegFileAlt size={18} className="ms-2" /> <p className='ms-2'> Nueva receta</p> </li>
                        <li onClick={() => router.push(`/dashboard/searchRecipes`)} className={`${router.pathname === "/dashboard/searchRecipes" ? "borderTrue" : ""} flex items-center  cursor-pointer nav-category  p-2 title-li`}> <TbFileSearch size={18} className="ms-2" /> <p className='ms-2'>Buscar receta</p> </li> */}
                        <li
                            onClick={() => router.push(`/dashboard/profile`)}
                            className={`${
                                router.pathname === "/dashboard/profile"
                                    ? "borderTrue"
                                    : ""
                            } flex items-center   p-2 cursor-pointer nav-category title-li`}
                        >
                            {" "}
                            <FaRegUser size={18} className="ms-2" />
                            <p className="ms-2">Medico</p>{" "}
                        </li>
                        <div className="flex justify-start items-center title-li p-2 cursor-pointer">
                            <RxExit size={18} />
                            <p className="ms-2">salir</p>
                        </div>
                    </ul>
                </div>
            </nav>

            <nav
                className={`sidebarDestok ${
                    screen ? "show" : ""
                } hidden lg:block `}
                data-trigger="scrollbar"
            >
                <div className={` sidebar-bodyDestok `}>
                    <div
                        onClick={() => {
                            setScreen(false);
                        }}
                        className="flex justify-end cursor-pointer"
                    >
                        <LuArrowLeftToLine size={30} className="m-2" />
                    </div>

                    <ul className="nav mt-3 ">
                        <li
                            onClick={() => router.push(`/dashboard/patiens`)}
                            className={`${
                                router.pathname === "/dashboard"
                                    ? "borderTrue"
                                    : ""
                            } nav-category flex items-center   title-li p-2`}
                        >
                            <IoHomeOutline size={20} className="ms-2" />{" "}
                            <p className="ms-2"> Paciente</p>{" "}
                        </li>
                        {/*     <li onClick={() => router.push(`/dashboard/newRecipes`)} className={`${router.pathname === "/dashboard/newRecipes" ? "borderTrue" : ""} nav-category flex items-center   title-li p-2`}>< FaRegFileAlt size={20} className="ms-2" /> <p className='ms-2'> Nueva receta</p> </li>
                        <li onClick={() => router.push(`/dashboard/searchRecipes`)} className={`${router.pathname === "/dashboard/searchRecipes" ? "borderTrue" : ""} nav-category flex items-center   title-li p-2`}> <TbFileSearch size={20} className="ms-2" /><p className='ms-2'>Buscar receta</p></li> */}
                        <li
                            onClick={() => router.push(`/dashboard/profile`)}
                            className={`${
                                router.pathname === "/dashboard/profile"
                                    ? "borderTrue"
                                    : ""
                            } nav-category flex items-center   title-li p-2`}
                        >
                            {" "}
                            <FaRegUser size={20} className="ms-2" />
                            <p className="ms-2">Medico</p>
                        </li>
                        <div className="flex justify-start items-center title-li p-2 cursor-pointer">
                            <RxExit size={18} />
                            <p className="ms-2">salir</p>
                        </div>
                    </ul>
                </div>
            </nav>
        </div>
    );
}
