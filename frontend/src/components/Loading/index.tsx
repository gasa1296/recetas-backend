import React from "react";
export default function Loading({ text }: { text?: string }) {
  return (
    <div className=" flex flex-col justify-center items-center min-h-[500px] ">
      <img src="/loading.gif" alt="Descripción del GIF" />
      <h3 className="text-[#424242] font-bold text-[40px] mt-5">
        {text || "Cargando"}
      </h3>
    </div>
  );
}
