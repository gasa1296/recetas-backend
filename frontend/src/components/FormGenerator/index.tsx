import React, { useEffect } from "react";
import { useForm } from "react-hook-form";
import { FieldComponents, getDefaultValues } from "./helper";
import { Field } from "@/types/Generals/FormGenerator";
import { yupResolver } from "@hookform/resolvers/yup";
import toast from "react-hot-toast";
interface Props {
  fields: Field[];
  submitData: (data: any) => void;
  buttonText?: string;
  loading?: boolean;
  renderButton?: (param: any) => JSX.Element;
  onFormChange?: (form: object) => void;
  isSubform?: number;
  reload?: boolean;
  setReload?: (reload: boolean) => void;
  schema?: any;
  externalError?: any;
  focus?: boolean;
}

export default function FormGenerator({
  fields,
  submitData,
  buttonText = "Enviar",
  loading = false,
  renderButton,
  onFormChange,
  isSubform,
  reload,
  setReload,
  schema,
  externalError = [],
  focus = false,
}: Props) {
  const {
    handleSubmit,
    register,
    formState: { errors },
    setValue,
    watch,
    setError,
    setFocus,
  } = useForm({
    resolver: schema ? yupResolver(schema) : undefined,
    defaultValues: getDefaultValues(fields, isSubform),
  });

  const allFields = watch();

  useEffect(() => {
    if (reload) {
      setReload && setReload(false);
      fields.forEach((field) => {
        if (field.type !== "subForm") {
          setValue(field.name, field.default);
        }
      });
    }
  }, [reload]);

  useEffect(() => {
    if (focus && fields.length > 0) {
      setFocus(fields[0].name);

      const firstFieldName = fields[0].name;

      const element = document.getElementById(firstFieldName);
      if (element) {
        element.scrollIntoView({ behavior: "smooth", block: "center" });
      }
    }
  }, [focus, fields, setFocus]);

  useEffect(() => {
    const hasNonEmptyValue = Object.values(allFields).some(
      (value) => value !== ""
    );

    if (hasNonEmptyValue && onFormChange && !reload) {
      onFormChange(allFields);
    }
  }, [allFields]);

  useEffect(() => {
    if (Object.keys(errors).length > 0)
      toast.error(
        "Hay un error en el formulario, asegurate de completar todos los campos"
      );
  }, [errors]);

  const customErrors = { ...errors, ...externalError };

  return (
    <form
      className="flex flex-wrap justify-between w-full"
      onSubmit={handleSubmit(submitData)}
    >
      {fields.map((field, index) => {
        const FieldComponent = FieldComponents[field.type];

        return (
          <FieldComponent
            key={index}
            externalError={customErrors[field.name]}
            error={customErrors[field.name]}
            register={register}
            setValue={setValue}
            watch={watch}
            isSubform={isSubform}
            setError={setError}
            {...field}
            label={
              isSubform && isSubform > 0
                ? `${field.secondLabel || field.label}`
                : `${field.label}`
            }
          />
        );
      })}
      {renderButton ? (
        renderButton(handleSubmit(submitData))
      ) : (
        <div className="w-full">
          <button
            disabled={loading}
            className="bg-[#000000]  disabled:opacity-40 p-3 text-[#EBF4F8] rounded-lg w-60 mx-auto block my-8"
            type="submit"
          >
            {buttonText}
          </button>
        </div>
      )}
    </form>
  );
}
