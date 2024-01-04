import { Field } from "@/types/Generals/FormGenerator";
import React from "react";

export default function InputSeparation({ width = 100 }: Field) {
    return (
        <div style={{ width: `${width}%` }} className="px-2 full-width">
            <p className="my-4 border-t-2 "></p>
        </div>
    );
}
