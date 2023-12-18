import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

export default function InputText({
    register,
    label,
    name,
    required,
    error,
}: Field) {
    return (
        <div className="my-3">
            <input
                id={name}
                name={name}
                type="checkbox"
                {...register(name, { required })}
                className={` form-check-input ${error && "border-danger"} fs-5`}
            />
            <label
                className={`ms-2 form-check-label title-form-generator  ${
                    error && "text-danger "
                }`}
                htmlFor={name}
            >
                {label}
            </label>
        </div>
    );
}
