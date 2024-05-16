import React from "react";
export default function Loading({
  text,
  textSize = 40,
  minHeight = 500,
}: {
  text?: string;
  textSize?: number;
  minHeight?: number;
}) {
  return (
    <div
      className={` flex flex-col justify-center items-center min-h-[${minHeight}px] `}
    >
      <img src="/loading.gif" alt="Descripción del GIF" />
      <h3 className={`text-[#424242] font-bold text-[${textSize}px] mt-5`}>
        {text || "Cargando"}
      </h3>
    </div>
  );
}
