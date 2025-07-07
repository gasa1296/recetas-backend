import React from "react";
import { useRouter } from "next/router";
import Image from "next/image";
import logo from "../../../assets/LogoFESA.svg";

export default function Nav() {
  const router = useRouter();
  return (
    <header className="py-8 flex justify-center border-b bg-white">
      <div className="container flex justify-center px-4">
        <Image
          src={logo}
          alt="Farmacias Especializadas FESA"
          width={200}
          height={50}
          className="h-12 w-auto object-contain"
        />
      </div>
    </header>
  );
}
