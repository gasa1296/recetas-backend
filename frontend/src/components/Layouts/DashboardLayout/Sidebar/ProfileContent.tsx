import { useAuthStore } from "@/store/auth";
import { getDateFormat } from "@/utils/getDateFormat";
import React from "react";

export default function ProfileContent() {
    const { user } = useAuthStore((state) => ({
        user: state.user,
        Logout: state.Logout,
    }));
    const { formattedTime, correctedDate } = getDateFormat("");
    return (
        <section>
            <div className=" justify-between items-center block md:flex ">
                <div className="mx-4">
                    <p className="text-[#1A1A1A] text-[24px] font-normal">
                        Bienvenido
                    </p>
                    <p className="text-[#1A1A1A] text-[18px] font-bold">
                        Dr. {user?.first_name} {user?.last_name1}
                    </p>
                </div>
            </div>
            <div className=" justify-between items-center block md:flex ">
                <div className="mx-4">
                    <p className="text-[#1A1A1A] text-[24px] font-bold">
                        {formattedTime}
                    </p>
                    <p className="text-[#1A1A1A] text-[18px] font-normal">
                        {correctedDate}
                    </p>
                </div>
            </div>
        </section>
    );
}
