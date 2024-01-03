import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

export default function InputRadioButton({
    register,
    label,
    name,
    required,
    error,
    options,
    disabledStyle,
    customChange,
    setValue,
    watch,
}: Field) {
    const values: any = watch();
    return (
        <div>
            <label
                className={`${error && "text-danger"} title-form-generator`}
                htmlFor={name}
            >
                {label}
            </label>
            {disabledStyle ? (
                <div className="form-check">
                    {options?.map((option, index) => (
                        <div key={index}>
                            <input
                                className="form-check-input"
                                value={option.value}
                                {...register(name, { required })}
                                type="radio"
                                name={name}
                                id={`${name}${index + 1}`}
                            />
                            <label
                                className="form-check-label"
                                htmlFor={`${name}${index + 1}`}
                            >
                                {option.label}
                            </label>
                        </div>
                    ))}
                </div>
            ) : (
                <div className="d-flex flex-wrap my-3">
                    {options?.map((option, index) => (
                        <div
                            key={index}
                            className=" btn btn-outline-secondary opacity-75  d-flex align-items-center me-2"
                        >
                            <input
                                value={option.value}
                                {...register(name, { required })}
                                onChange={(e) => {
                                    setValue(name, e.target.value);
                                    customChange &&
                                        customChange({
                                            setValue,
                                            newValue: e.target.value,
                                            values,
                                        });
                                }}
                                name={name}
                                className="form-check-input mt-0"
                                type="radio"
                                id={`${name}${index + 1}`}
                            />
                            <label
                                className="text-gray m-0 ms-2 "
                                htmlFor={`${name}${index + 1}`}
                            >
                                {option.label}
                            </label>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
