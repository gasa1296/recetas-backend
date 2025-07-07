import React from "react";
import { HiPlusSmall } from "react-icons/hi2";
interface Props {
  setShow: any;
}
export default function GenericNotFound({ setShow }: Props) {
  return (
    <section className="  text-black text-center ">
      <div className="text-left">
        <span className="font-bold"> No se encontraron resultados</span> para tu
        búsqueda.
      </div>
    </section>
  );
}
