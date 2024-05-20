import React, { useEffect } from "react";

export default function useScrollToTop(topSeparation = 0, fieldName?: string) {
  // Función para detectar el scroll
  const toggleVisibility = () => {
    scrollToTop();
  };

  // Función para hacer scroll hacia arriba
  const scrollToTop = () => {
    if (fieldName) {
      const element = document.getElementById(fieldName);
      if (element) {
        element.scrollIntoView({ behavior: "smooth", block: "center" });
        element.focus && element.focus();
      }
    } else {
      window.scrollTo({
        top: topSeparation,
        behavior: "smooth", // Efecto de desplazamiento suave
      });
    }
  };

  // Escuchar el evento scroll
  useEffect(() => {
    scrollToTop();
  }, []);
}
