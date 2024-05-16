import { Field } from "@/types/Generals/FormGenerator";
import React from "react";
import { FieldComponents } from "../helper";
import Collapse from "@/components/Collapse";

export default function InputCollapseForm({
  watch,
  width = 100,
  label,
  form,
  setValue,
  register,
  setError,
  externalError,
}: Field) {
  return (
    <div className="px-2  flex flex-wrap justify-between w-full">
      <Collapse title={label} form={form}>
        {form.map((field: Field, index: number) => {
          const FieldComponent = FieldComponents[field.type];

          return (
            <FieldComponent
              key={index}
              externalError={externalError}
              //error={customErrors[field.name]}
              register={register}
              setValue={setValue}
              watch={watch}
              setError={setError}
              {...field}
              label={field.label}
            />
          );
        })}
      </Collapse>
    </div>
  );
}
