import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

export default function InputNumber({
    register,
    label,
    name,
    required,
    setValue,
    error,
    watch,
    visible,
    customChange,
    width = 100,
}: Field) {
    const values: any = watch();
    if (visible && !values[visible]) return <> </>;
    return (
        <div className="px-2 full-width" style={{ width: `${width}%` }}>
            <label
                className={`${error && "text-red-400"} title-form-generator`}
                htmlFor={name}
            >
                {label}
            </label>
            <input
                id={name}
                name={name}
                type="number"
                {...register(name, { required })}
                className={`w-full form-control my-2 text-[16px] m-0 p-3 rounded-md border-[#DBE2EA] border-2 focus:outline-none ${
                    error && "border-red-400 "
                }`}
                /* onChange={(e) => {
                    setValue(name, e.target.value);
                    customChange &&
                        customChange({
                            setValue,
                            newValue: e.target.value,
                            values,
                        });
                }} */
            />
        </div>
    );
}
