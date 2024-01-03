import {Language} from "@/types/Languages";

const getInitLanguage = (): Language => {
  let INIT_LANGUAGE: Language | null;
  const DEFAULT_LANGUAGE: Language = "es";

  if (typeof window !== "undefined") {
    INIT_LANGUAGE = localStorage.getItem("[LANGUAGE]") as Language;
    return INIT_LANGUAGE ?? DEFAULT_LANGUAGE;
  }

  return DEFAULT_LANGUAGE;
};

export {getInitLanguage};
