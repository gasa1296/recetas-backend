import React, { useState } from "react";
import Nav from "./Nav";
import Footer from "./Footer";
import SideBar from "./Sidebar";
interface Props {
    children: React.ReactNode;
}
export default function DashboardLayout({ children }: Props) {
    const [screen, setScreen] = useState(false);
    return (
        <>
            <Nav screen={screen} setScreen={setScreen} />
            <div className="flex bg-[#F7F7F7]">
                <SideBar screen={screen} setScreen={setScreen} />
                <div className="w-full min-h-[60vh]">{children}</div>
            </div>

            <Footer />
        </>
    );
}
