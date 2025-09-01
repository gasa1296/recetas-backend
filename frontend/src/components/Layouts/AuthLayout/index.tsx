import React from "react";
import Nav from "./Nav";
import Footer from "./Footer";
interface Props {
  children: React.ReactNode;
}

export default function AuthLayout({ children }: Props) {
  return (
    <div>
      <Nav />
      {children}
      <Footer />
    </div>
  );
}
