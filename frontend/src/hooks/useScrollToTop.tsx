import React, { useEffect } from "react";

export default function useScrollToTop() {
  // Función para detectar el scroll
  const toggleVisibility = () => {
    scrollToTop();
  };

  // Función para hacer scroll hacia arriba
  const scrollToTop = () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth", // Efecto de desplazamiento suave
    });
  };

  // Escuchar el evento scroll
  useEffect(() => {
    scrollToTop();
  }, []);
}
