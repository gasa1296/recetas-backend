import { useRouter } from "next/router";
import React from "react";
interface Props {
    title: string;
    Icon: any;
    path: string;
}

export default function SidebarItem({ Icon, title, path }: Props) {
    const router = useRouter();
    const isSelected = router.pathname === path ? "borderTrue" : "";
    return (
        <li
            onClick={() => router.push(path)}
            className={`${isSelected} nav-category flex items-center title-li h-[42px] border-l-4 border-l-transparent`}
        >
            <Icon size={18} className="ms-2" />
            <p className="ms-2"> {title}</p>
        </li>
    );
}
