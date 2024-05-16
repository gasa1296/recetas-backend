import Image from "next/image";
import React, { ChangeEvent, useEffect, useRef, useState } from "react";
import { toast } from "react-hot-toast";
import { AiFillCamera, AiFillCloseCircle } from "react-icons/ai";
import { handleGetFiles, isHttp } from "../helper";
import { Field } from "@/types/Generals/FormGenerator";

export default function InputFile({
  label,
  name,
  error,
  setValue,
  maxFile = 999,
  subLabel,
  watch,
  customChange,
  temporalName = "",
  width,
}: Field) {
  const values: any = watch();
  const [files, setFiles] = useState<File[]>([]);
  const [previews, setPreviews] = useState<string[]>([]);
  const MAX_UPLOAD_ITEMS: number = maxFile || 5;
  const MAX_SIZE_FILE = 10;
  const fileRef = useRef<HTMLInputElement>(null);

  const onDragOver = (event: React.DragEvent): void => {
    event.preventDefault();
    event.stopPropagation();
  };

  const onDragLeave = (event: React.DragEvent): void => {
    event.preventDefault();
    event.stopPropagation();
  };

  const onDrop = (event: React.DragEvent): void => {
    event.preventDefault();
    event.stopPropagation();
    const files = event.dataTransfer.files;
    processFiles(files);
  };

  const processFiles = (inputFiles: FileList) => {
    const fileArray = Array.from(inputFiles);

    fileArray.forEach((file, index) => {
      if (
        previews.length + index < MAX_UPLOAD_ITEMS &&
        file.type.startsWith("image/")
      ) {
        const preview: string = URL.createObjectURL(file);
        customChange &&
          customChange({ setValue, newValue: [...files, file], values });
        setValue(name, [...files, file]);
        setFiles((prev) => [...prev, file]);
        setPreviews((prev) => [...prev, preview]);
      } else {
        toast.error(
          `Solo puedes subir ${MAX_UPLOAD_ITEMS} ${
            MAX_UPLOAD_ITEMS > 1 ? "fotos" : "foto"
          }`
        );
      }
    });
  };

  const onChangeFile = (event: ChangeEvent<HTMLInputElement>): void => {
    const { files: inputFiles } = event.target;

    if (!inputFiles?.length || !inputFiles) return;
    const file = inputFiles[0];

    //validate file size
    if (file.size > MAX_SIZE_FILE * 1024 * 1024) {
      toast.error(`El peso máximo por foto es de ${MAX_SIZE_FILE}MB`);
      return;
    }

    if (previews?.length === MAX_UPLOAD_ITEMS) {
      toast.error(
        `Solo puedes subir ${MAX_UPLOAD_ITEMS} ${
          MAX_UPLOAD_ITEMS > 1 ? "fotos" : "foto"
        } `
      );
      return;
    }

    const preview: string = URL.createObjectURL(file);
    customChange &&
      customChange({ setValue, newValue: [...files, file], values });
    setValue(name, [...files, file]);
    setFiles((prev) => [...prev, file]);
    setPreviews((prev) => [...prev, preview]);
  };

  const onDelete = (index: number): void => {
    setValue(
      name,
      files.filter((_, key) => key !== index)
    );
    customChange &&
      customChange({
        setValue,
        newValue: files.filter((_, key) => key !== index),
        values,
      });
    setPreviews((prev) => prev.filter((_, key) => key !== index));
    setFiles((prev) => prev.filter((_, key) => key !== index));
  };

  const handleParseFiles = async () => {
    let previews = [];
    try {
      if (!values[name]) return;

      previews = values[name].map((image: any) => {
        if (typeof image !== "string") return URL.createObjectURL(image);
        return isHttp(image)
          ? image
          : `${process.env.NEXT_PUBLIC_BASE_URL}/storage/${image}`;
      });

      setPreviews(previews);

      const files = await handleGetFiles(values[name]);

      setFiles(files as any);
      setValue(name, files);
    } catch (error) {
      setFiles(previews);
      setValue(name, previews);
    }
  };

  useEffect(() => {
    const timeoutId = setTimeout(() => {
      handleParseFiles();
      if (temporalName) setValue(temporalName, null);
    }, 200);

    return () => clearTimeout(timeoutId);
  }, [values[temporalName]]);

  return (
    <div style={{ width: `${width}%` }} className="px-2 full-width">
      <label
        className={`${error && "text-red-400"} title-form-generator  mt-2`}
        htmlFor={name}
      >
        {label} (Peso máximo por foto 10MB)
      </label>
      {subLabel === "" ? null : <p className="  fs-6 mb-0">{subLabel}</p>}
      <input
        id={name}
        name={name}
        accept="image/*"
        type="file"
        onChange={(event) => {
          onChangeFile(event);
        }}
        style={{ display: "none" }}
        ref={fileRef}
      />
      <div className="mt-4 w-100 d-flex flex-wrap mb-4">
        {previews.map((preview, index) => (
          <div className="me-2 mb-2 relative  w-[150px] " key={index}>
            <Image
              width={150}
              height={150}
              style={{ objectFit: "cover" }}
              className="rounded"
              src={preview}
              alt="preview"
              key={index}
            />
            <button
              onClick={(e) => {
                e.preventDefault();
                onDelete(index);
              }}
              className="absolute z-[0] top-0 right-0  mt-2 me-2 p-1 pt-0"
            >
              <AiFillCloseCircle size={20} color="red" />
            </button>
          </div>
        ))}

        {previews.length < maxFile && (
          <div
            className="drop-area" // Añade las clases de estilo necesarias
            onDragOver={onDragOver}
            onDragLeave={onDragLeave}
            onDrop={onDrop}
          >
            <button
              className={` bg-white mx-auto  ${
                error && "border-red-400"
              } border-separate border-2`}
              style={{
                width: "150px",
                height: "150px",
                borderStyle: "dashed",
              }}
              onClick={(e) => {
                e.preventDefault();
                fileRef.current?.click();
              }}
            >
              <AiFillCamera
                size={40}
                color="#313131"
                className=" mx-auto block"
              />
              <p className=" text-[14px] px-2 text-[#5C5C5C]">
                Agrega o arrastra tus fotos aquí
              </p>
            </button>
          </div>
        )}
      </div>
    </div>
  );
}
