import React from "react";
import Tooltip from "../Components/Tooltip";
import { Field } from "@/types/Generals/FormGenerator";
import Image from "next/image";

export default function InputRecetas({
  register,
  label,
  name,
  required,
  error,
  setValue,
  visible,
  watch,
  tooltip,
  width = 100,
  recetasOptions,
  customChange,
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

      <div className="flex flex-wrap justify-center lg:justify-between pt-6 w-full">
        {recetasOptions?.map((option, index) => (
          <div className="mb-5">
            <Image
              src={option.image}
              alt={`image-${index}`}
              width={320}
              height={320}
              className="  h-[310px] w-[310px]  object-contain rounded "
            />
            <div className="bg-white max-w-[200px] w-full px-4 py-3 mx-auto mt-5 rounded border border-[#DBE2EA]">
              <input
                type="radio"
                name={name}
                id={`${name}${index + 1}`}
                value={option.value}
                className="ml-2"
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
              />
              <label className="ml-2" htmlFor={`${name}${index + 1}`}>
                Elegir este diseño
              </label>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
