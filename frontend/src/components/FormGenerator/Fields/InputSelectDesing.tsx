import React from "react";
import { Field } from "@/types/Generals/FormGenerator";
import Image from "next/image";
import Receta1 from "@/assets/images/recetas/Receta1.png";
import Receta2 from "@/assets/images/recetas/Receta2.png";
import Receta3 from "@/assets/images/recetas/Receta3.png";
export default function InputSelectDesing({
    register,
    label,
    name,
    required,
    error,
    options,
    disabledStyle,
    customChange,
    setValue,
    watch,
}: Field) {
    const values: any = watch();

    return (
        <div>
            <div className="flex justify-between">
                <Image
                    src={Receta1}
                    alt="receta1"
                    className=" h-[290px] w-[1366px] d-block mx-auto "
                />
                <Image
                    src={Receta2}
                    alt="receta2"
                    className=" h-[290px] w-[1366px] d-block mx-auto "
                />
                <Image
                    src={Receta3}
                    alt="receta3"
                    className=" h-[290px] w-[1366px] d-block mx-auto "
                />
            </div>
        </div>
    );
}
