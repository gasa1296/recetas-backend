import React from "react";
export default function LoadingModal({ text }: { text?: string }) {
  return (
    <div className=" flex flex-col justify-center items-center min-h-[500px] border fixed top-0 left-0 w-full h-full bg-[rgba(0,0,0,.5)] z-[999]">
      <img src="/loading.gif" alt="Descripción del GIF" />
      <h3 className="text-[#f9f9f9] font-bold text-[40px] mt-5 px-2 text-center">
        {text || "Cargando"}
      </h3>
    </div>
  );
}
