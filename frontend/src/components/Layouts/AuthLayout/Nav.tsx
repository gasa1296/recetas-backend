import React from "react";
import fondo from "../../../assets/Header-img2x.png";
import Image from "next/image";
import { useRouter } from "next/router";

export default function Nav() {
  const router = useRouter();
  return (
    <nav className="bg-[#F7F7F7]   ">
      <Image
        onClick={() => router.push("/")}
        src={fondo}
        alt="fondo-home"
        className="  max-w-[1366px] w-full mx-auto object-contain"
      />
    </nav>
  );
}
