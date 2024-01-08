import { Field } from "@/types/Generals/FormGenerator";
import React from "react";
import { FieldComponents } from "../helper";
import FormGenerator from "..";
import { validateSameObject } from "@/utils/isSameObject";

export default function InputSubform({
    register,
    name,
    error,
    watch,
    width = 100,
    form,
    setValue,
    buttonAddText = "Agregar",
    maxFile = 0,
    externalError,
}: Field) {
    const values = watch();
    const [reload, setReload] = React.useState(false);

    const handleAdd = (e: any) => {
        e.preventDefault();

        const newForm: any = {};

        form.map((field: Field) => {
            if (field.type !== "title") newForm[field.name] = "";
        });

        setValue(name, [...values[name], newForm]);
    };

    const handleRemove = (index: number) => {
        setReload(true);
        setValue(
            name,
            values[name].filter((_: any, index2: number) => index !== index2)
        );
    };

    return (
        <div style={{ width: `${width}%` }} className="px-2 full-width">
            {values[name].map((subform: any, index: number) => {
                return (
                    <div
                        className="flex flex-wrap justify-between w-full relative pt-4"
                        key={index}
                    >
                        {index >= maxFile && (
                            <>
                                {" "}
                                <div className="  absolute right-0 flex lg:hidden  justify-end">
                                    <button
                                        onClick={(e) => {
                                            e.preventDefault();
                                            handleRemove(index);
                                        }}
                                        className="bg-red-500 w-[30px] h-[30px] text-[#EBF4F8]  rounded-full"
                                        type="submit"
                                    >
                                        X
                                    </button>
                                </div>
                                <div className="hidden lg:absolute right-0 lg:flex w-full justify-end">
                                    <button
                                        onClick={(e) => {
                                            e.preventDefault();
                                            handleRemove(index);
                                        }}
                                        className="bg-red-500 px-3 py-1 text-[#EBF4F8]  rounded-full"
                                        type="submit"
                                    >
                                        Remover
                                    </button>
                                </div>{" "}
                            </>
                        )}

                        <FormGenerator
                            submitData={() => {}}
                            externalError={
                                externalError ? externalError[index] : {}
                            }
                            fields={form.map((field: Field) => ({
                                ...field,
                                default: subform[field.name],
                            }))}
                            reload={reload}
                            setReload={setReload}
                            loading={false}
                            isSubform={index}
                            onFormChange={(form) => {
                                if (validateSameObject(subform as object, form))
                                    setValue(name, [
                                        ...values[name].map(
                                            (value: any, index2: number) =>
                                                index === index2 ? form : value
                                        ),
                                    ]);
                            }}
                            buttonText="Continuar"
                            renderButton={(handleSubmit) => <> </>}
                        />
                    </div>
                );
            })}
            <button
                onClick={handleAdd}
                className="bg-[#000000] p-3 text-[#EBF4F8] rounded-lg max-w-[240px] w-full mx-auto block my-20"
                type="submit"
            >
                {buttonAddText}
            </button>
        </div>
    );
}
