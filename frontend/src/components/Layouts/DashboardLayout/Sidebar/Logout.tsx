import { useAuthStore } from "@/store/auth";
import { useRouter } from "next/router";
import React from "react";
import { RxExit } from "react-icons/rx";
export default function Logout() {
    const router = useRouter();
    const Logout = useAuthStore((state) => state.Logout);
    return (
        <div
            onClick={async () => {
                await Logout();
                router.push(`/`);
            }}
            className="flex justify-center w-full items-center title-li  cursor-pointer absolute bottom-4 "
        >
            <RxExit size={18} />
            <p className="ms-2">Salir</p>
        </div>
    );
}
