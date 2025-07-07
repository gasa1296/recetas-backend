import React from "react";
import Nav from "./Nav";
import Footer from "./Footer";
interface Props {
  children: React.ReactNode;
}

export default function AuthDesignLayout({ children }: Props) {
  return (
    <div className="min-h-screen flex flex-col bg-slate-50">
      <Nav />
      <div
        className="flex-grow bg-cover bg-center "
        style={{
          backgroundImage: "url('/login/background.png')", // Replace with your actual image path
        }}
      >
        <div className="w-full h-full bg-[#F4F9FC]/50">{children}</div>
      </div>
      <Footer />
    </div>
  );
}
