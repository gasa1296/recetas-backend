import React, { useEffect } from "react";
import { useForm } from "react-hook-form";
import { FieldComponents, getDefaultValues } from "./helper";
import { Field } from "@/types/Generals/FormGenerator";
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
}: Props) {
    const {
        handleSubmit,
        register,
        formState: { errors },
        setValue,
        watch,
    } = useForm({
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
        const hasNonEmptyValue = Object.values(allFields).some(
            (value) => value !== ""
        );

        if (hasNonEmptyValue && onFormChange && !reload) {
            onFormChange(allFields);
        }
    }, [allFields]);

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
                        error={errors[field.name]}
                        register={register}
                        setValue={setValue}
                        watch={watch}
                        {...field}
                    />
                );
            })}
            {renderButton ? (
                renderButton(handleSubmit(submitData))
            ) : (
                <button
                    disabled={loading}
                    className="bg-[#000000] disabled:opacity-40 p-3 text-[#EBF4F8] rounded-lg w-60 mx-auto block my-8"
                    type="submit"
                >
                    {buttonText}
                </button>
            )}
        </form>
    );
}
