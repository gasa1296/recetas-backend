import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

export default function InputSubTitle({ label, width = 100 }: Field) {
  return (
    <div style={{ width: `${width}%` }} className="px-2 full-width">
      <p className=" font-bold text-[#000] text-[18px] text-start mb-4">
        {label}
      </p>
    </div>
  );
}
