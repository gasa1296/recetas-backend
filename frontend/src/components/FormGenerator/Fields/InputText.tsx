import React, { useRef, useState } from "react";
import Tooltip from "../Components/Tooltip";
import { Field } from "@/types/Generals/FormGenerator";

export default function InputText({
    register,
    label,
    name,
    required,
    error,
    subLabel,
    disabled,
    visible,
    watch,
    tooltip,
    width = 100,
}: Field) {
    const values: any = watch();

    if (visible && !values[visible]) return <> </>;

    return (
        <div style={{ width: `${width}%` }} className="px-2 full-width">
            <label
                className={`${
                    error && "text-red-400"
                }  flex relative text-[#1A1A1A] text-[16px] `}
            >
                {label}
                {tooltip && <Tooltip tooltip={tooltip} />}
            </label>
            {subLabel === "" ? null : (
                <p
                    className={` text-[#1A1A1A] text-[16px] mb-0 ${
                        error && "text-red-400"
                    }`}
                >
                    {subLabel}
                </p>
            )}

            <input
                id={name}
                name={name}
                disabled={disabled}
                type="text"
                {...register(name, { required })}
                className={`w-full form-control my-2 text-[16px] m-0 p-3 rounded-md border-[#DBE2EA] border-2 focus:outline-none ${
                    error && "border-red-400 "
                }`}
            />
        </div>
    );
}
