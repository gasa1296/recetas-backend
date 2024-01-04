import React from "react";
import { LuArrowLeftToLine } from "react-icons/lu";
import SidebarItem from "./SidebarItem";
import Logout from "./Logout";
import ProfileContent from "./ProfileContent";

interface Props {
    setScreen: (value: boolean) => void;
    menuOptions: { title: string; Icon: any; path: string }[];
    screen: boolean;
}
export default function SidebarWeb({ setScreen, menuOptions, screen }: Props) {
    const isShow = screen ? "show " : "";
    return (
        <nav className={`sidebar relative ${isShow}`} data-trigger="scrollbar ">
            <div className={`overlay sidebar-body  ${isShow}`}>
                <div
                    onClick={() => setScreen(false)}
                    className="flex justify-end cursor-pointer"
                >
                    <LuArrowLeftToLine size={30} className="m-2" />
                </div>

                <ProfileContent />

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
