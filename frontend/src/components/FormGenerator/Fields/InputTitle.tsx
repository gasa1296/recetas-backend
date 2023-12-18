import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

export default function InputTitle({ label, width = 100 }: Field) {
    return (
        <div style={{ width: `${width}%` }} className="px-2 full-width">
            <p className=" font-bold text-[#4B4B4B] text-[24px] text-start mb-4">
                {label}
            </p>
        </div>
    );
}
