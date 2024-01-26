import React from "react";
export default function Loading() {
  return (
    <div className=" flex flex-col justify-center items-center min-h-[500px] ">
      <img src="/loading.gif" alt="Descripción del GIF" />
      <h3 className="text-[#424242] font-bold text-[40px] mt-5">Cargando</h3>
    </div>
  );
}
