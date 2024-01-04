import React from "react";
import { LuArrowLeftToLine } from "react-icons/lu";
import SidebarItem from "./SidebarItem";
import Logout from "./Logout";

interface Props {
    setScreen: (value: boolean) => void;
    menuOptions: { title: string; Icon: any; path: string }[];
    screen: boolean;
}
export default function SidebarMobile({
    setScreen,
    menuOptions,
    screen,
}: Props) {
    const isShow = screen ? "show " : "";
    return (
        <nav
            className={`sidebarDestok ${isShow} hidden md:block relative `}
            data-trigger="scrollbar"
        >
            <div className={` sidebar-bodyDestok `}>
                <div
                    onClick={() => setScreen(false)}
                    className="flex justify-end cursor-pointer"
                >
                    <LuArrowLeftToLine size={30} className="m-2" />
                </div>

                <ul className="nav mt-3 ">
                    {menuOptions.map((option) => (
                        <SidebarItem {...option} />
                    ))}
                </ul>
                <Logout />
            </div>
        </nav>
    );
}
