import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

export default function InputTextarea({
    register,
    label,
    name,
    required,
    error,
    subLabel,
    width = 100,

}: Field) {
    return (
        <div style={{ width: `${width}%` }} className="px-2 full-width">
            <label
                className={`${error && "text-red-400"} title-form-generator flex relative text-[#1A1A1A] text-[16px]`}
                htmlFor={name}
            >
                {label}
            </label>
            {subLabel === "" ? null : (
                <p
                    className={`text-[#1A1A1A] text-[16px] mb-0 ${error && "text-red-400"
                        }`}
                >
                    {subLabel}
                </p>
            )}

            <textarea
                {...register(name, { required })}
                className={`w-full form-control my-2 text-[16px] m-0 p-3 rounded-md border-[#DBE2EA] border-2 focus:outline-none ${error && "border-red-400 "
                    }`}
                id={name}
                name={name}
                cols={5}
                rows={5}
            ></textarea>
        </div>
    );
}
