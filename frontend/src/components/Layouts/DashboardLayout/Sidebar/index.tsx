import React from "react";
import { FaRegUser } from "react-icons/fa";
import { IoHomeOutline } from "react-icons/io5";
import SidebarWeb from "./SidebarWeb";
import SidebarMobile from "./SidedarMobile";
export default function SideBar({ screen, setScreen }: any) {
    const menuOptions = [
        {
            title: "Paciente",
            path: "/dashboard",
            Icon: FaRegUser,
        },
        { title: "Médico", path: "/dashboard/profile", Icon: FaRegUser },
    ];

    return (
        <div className="main-wrapper  ">
            <SidebarWeb
                screen={screen}
                setScreen={setScreen}
                menuOptions={menuOptions}
            />
            <SidebarMobile
                screen={screen}
                setScreen={setScreen}
                menuOptions={menuOptions}
            />
        </div>
    );
}
