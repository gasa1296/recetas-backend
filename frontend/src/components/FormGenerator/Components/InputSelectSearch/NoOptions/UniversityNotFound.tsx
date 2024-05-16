import React from "react";
import { HiPlusSmall } from "react-icons/hi2";
interface Props {
  setShow: any;
}
export default function UniversityNotFound({ setShow }: Props) {
  return (
    <section className="  text-black text-center ">
      <div className="text-left">
        <span className="font-bold"> No se encontraron resultados</span> para tu
        búsqueda. Intenté con otra búsqueda o{" "}
        <span className="font-bold">elige una nueva universidad</span> agregando
        su información.
      </div>
      <div className=" flex flex-wrap items-center mt-3 justify-center">
        <button
          onClick={(e) => {
            e.preventDefault();

            setShow(true);
          }}
          className="flex justify-center items-center button-BlacK p-2 px-3"
        >
          <HiPlusSmall size={25} />
          Agregar nueva
        </button>
      </div>
    </section>
  );
}
