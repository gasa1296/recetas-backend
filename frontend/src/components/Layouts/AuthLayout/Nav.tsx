import React from "react";
import { useRouter } from "next/router";

export default function Nav() {
  const router = useRouter();
  return (
    <nav className="bg-[#F7F7F7]   ">
      <img
        onClick={() => router.push("/")}
        src={"/logo-display.png"}
        width={1000}
        height={1000}
        alt="fondo-home"
        className="  max-w-[1366px] w-full mx-auto object-contain"
      />
    </nav>
  );
}
