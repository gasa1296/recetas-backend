import React, { useEffect, useState } from "react";
import Tooltip from "../Components/Tooltip";
import { Field } from "@/types/Generals/FormGenerator";
import { MdDelete } from "react-icons/md";
import toast from "react-hot-toast";

export default function InputMultiPhone({
  label,
  name,
  required,
  error,
  subLabel,
  disabled,
  visible,
  watch,
  tooltip,
  maxFile,
  setValue,
  width = 100,
}: Field) {
  const values: any = watch();

  const [phoneNumbers, setPhoneNumbers] = useState(
    typeof values[name] === "string" ? [values[name]] : values[name] || [""]
  );

  const handlePhoneNumberChange = (index: number, newValue: string) => {
    const newPhoneNumbers = [...phoneNumbers];
    newPhoneNumbers[index] = newValue;
    setPhoneNumbers(newPhoneNumbers);
  };

  const addPhoneNumberField = () => {
    setPhoneNumbers([...phoneNumbers, ""]);
  };

  const removePhoneNumberField = (index: number) => {
    setPhoneNumbers(
      phoneNumbers.filter((_: string, idx: number) => idx !== index)
    );
  };

  useEffect(() => {
    setValue(name, phoneNumbers);
  }, [phoneNumbers]);

  const findFirstDuplicate = (numbers: String[]) => {
    let seen = new Set();
    for (const number of numbers) {
      if (seen.has(number)) {
        return number;
      }
      seen.add(number);
    }
    return null;
  };

  if (visible && !values[visible]) return <></>;

  const duplicateNumber = findFirstDuplicate(phoneNumbers);

  return (
    <>
      {phoneNumbers.map((phoneNumbers: string, index: number) => (
        <div
          key={`phone-${index}`}
          style={{ width: `${width}%` }}
          className="px-2 full-width relative"
        >
          <label
            className={`${
              ((error && error[index]?.message) ||
                duplicateNumber === phoneNumbers) &&
              "text-red-400"
            }  flex relative text-[#1A1A1A] text-[16px] `}
          >
            {label} {index + 1} {required && "*"}
            {tooltip && <Tooltip tooltip={tooltip} />}
          </label>
          {subLabel === "" ? null : (
            <p
              className={` text-[#1A1A1A] text-[16px] mb-0 ${
                ((error && error[index]?.message) ||
                  duplicateNumber === phoneNumbers) &&
                "text-red-400"
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
            maxLength={maxFile}
            onChange={(e) => handlePhoneNumberChange(index, e.target.value)}
            value={phoneNumbers}
            className={`w-full form-control my-2 text-[16px] m-0 p-3 rounded-md border-[#DBE2EA] border-2 focus:outline-none ${
              ((error && error[index]?.message) ||
                duplicateNumber === phoneNumbers) &&
              "border-red-400 "
            }`}
          />

          {index > 0 && !disabled && (
            <button
              className="absolute top-0 right-3 flex   justify-end z-10"
              type="button"
              onClick={() => removePhoneNumberField(index)}
            >
              <MdDelete color="#e14141" size={20} />
            </button>
          )}
        </div>
      ))}
      {!disabled && (
        <div className="w-full text-center">
          <button
            type="button"
            onClick={addPhoneNumberField}
            className="my-2 bg-[#000000]  text-[#EBF4F8] max-w-[300px] h-[40px] rounded-full w-full mx-2"
          >
            Agregar otro número de teléfono
          </button>
        </div>
      )}
    </>
  );
}
