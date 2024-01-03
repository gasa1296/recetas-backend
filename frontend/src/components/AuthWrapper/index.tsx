import { useRouter } from "next/router";
import React, { useEffect, useState } from "react";
import { validateAuthPath } from "./helper";
import { useAuthStore } from "@/store/auth";
import AuthLayout from "../Layouts/AuthLayout";

interface Props {
    children: JSX.Element;
}
export default function AuthWrapper({ children }: Props) {
    const [active, setActive] = useState(false);
    const [loading, setLoading] = useState(true);
    const isAuth = useAuthStore((state) => state.isAuth);
    const RecoverUser = useAuthStore((state) => state.RecoverUser);

    const router = useRouter();

    const getAuthSession = async () => {
        const token = await localStorage.getItem("sessionToken");
        if (!token) {
            setActive(true);
            return setTimeout(() => setLoading(false), 1000);
        }

        await RecoverUser();
        router.push("/dashboard");

        setTimeout(() => setLoading(false), 1000);
        setActive(true);
    };

    useEffect(() => {
        getAuthSession();
    }, []);

    useEffect(() => {
        if (active) {
            if (validateAuthPath(router.pathname, isAuth)) router.push("/");
        }

        //eslint-disable-next-line
    }, [router.pathname, isAuth, active]);

    if (loading)
        return (
            <AuthLayout>
                <div className="border flex justify-center items-center min-h-[500px] ">
                    <h3 className="text-[#424242] font-bold text-[40px]">
                        Cargando ...
                    </h3>
                </div>
            </AuthLayout>
        );

    return <>{children}</>;
}
