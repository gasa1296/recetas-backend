import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

export default function InputSelect({
  register,
  label,
  name,
  required,
  error,
  options,
  setValue,
  customChange,
  watch,
  disabled,
  width = 100,
}: Field) {
  const values: any = watch();
  return (
    <div className="px-2 full-width" style={{ width: `${width}%` }}>
      <label
        className={`${error && "text-red-400"} title-form-generator `}
        htmlFor={name}
      >
        {label}
      </label>

      <select
        disabled={disabled}
        id={name}
        name={name}
        {...register(name, { required })}
        className={`disabled:opacity-70 w-full form-control my-2 text-[16px] m-0 p-3 rounded-md border-[#DBE2EA] border-2 focus:outline-none ${
          error && "border-red-400 "
        }`}
        /*  onChange={(e) => {
                    setValue(name, e.target.value);
                    customChange &&
                        customChange({
                            setValue,
                            newValue: e.target.value,
                            values,
                        });
                }} */
      >
        <option selected></option>
        {options?.map((option, index) => (
          <option key={index} value={option.value}>
            {option.label}
          </option>
        ))}
      </select>
    </div>
  );
}
