import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

export default function InputNumber({
  register,
  label,
  name,
  required,
  setValue,
  disabled,
  error,
  watch,
  visible,
  customChange,
  width = 100,
  max,
  min,
}: Field) {
  const values: any = watch();
  if (visible && !values[visible]) return <> </>;
  return (
    <div className="px-2 full-width relative" style={{ width: `${width}%` }}>
      <label
        className={`${error && "text-red-400"} title-form-generator`}
        htmlFor={name}
      >
        {label} <br />{" "}
      </label>
      <input
        id={name}
        name={name}
        type="number"
        disabled={disabled}
        {...register(name, {
          required,
          validate: (value: string) => {
            if (max && Number(value) > max) {
              return `El valor máximo es ${max}`;
            }
            if (min && Number(value) < min) {
              return `El valor minimo es ${min}`;
            }
          },
        })}
        className={`w-full form-control my-2 text-[16px] m-0 p-3 rounded-md border-[#DBE2EA] border-2 focus:outline-none ${
          error && "border-red-400 "
        }`}
      />
      {error && error?.message && (
        <span className="text-[12px] text-red-400 absolute -bottom-3 w-full left-2">
          {`(${error?.message})`}{" "}
        </span>
      )}
    </div>
  );
}
