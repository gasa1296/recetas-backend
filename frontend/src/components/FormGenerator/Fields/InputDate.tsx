import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

export default function InputDate({
    register,
    label,
    name,
    required,
    error,
    minDate = "",
    maxDate = "",
    watch,
    limitDays,
    width = 100,

}: Field) {
    const values: any = watch();

    const getMaxDate = () => {
        // Si no se provee una fecha máxima, usamos la fecha actual
        const baseMaxDate = new Date(values[maxDate] || new Date());

        if (!limitDays) {
            return baseMaxDate;
        }

        // Calculamos la fecha máxima teniendo en cuenta limitDate
        return new Date(
            baseMaxDate.getTime() - limitDays * 24 * 60 * 60 * 1000
        );
    };

    return (
        <div style={{ width: `${width}%` }} className="px-2 full-width">
            <label
                className={`${error && "text-red-600"
                    } title-form-generator my-1 flex relative text-[#1A1A1A] text-[16px]`}
                htmlFor={name}
            >
                {label}
            </label>

            <div className="flex ">
                <div className=" flex ">
                    <input
                        type="datetime-local"
                        min={new Date(values[minDate] || null)
                            .toISOString()
                            .slice(0, 16)}
                        max={getMaxDate().toISOString().slice(0, 16)}
                        className={`w-full form-controltext-[16px] m-0 p-3 rounded-md border-[#DBE2EA] border-2 focus:outline-none ${error && "border-red-600 "
                            }`}
                        {...register(name, { required })}
                    />
                </div>
            </div>
        </div>
    );
}
