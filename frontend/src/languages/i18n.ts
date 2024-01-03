import i18next from "i18next";
import { initReactI18next } from "react-i18next";
import lang_es from "./en/lang.json";
import lang_en from "./en/lang.json";
import { getInitLanguage } from "@/utils/getInitialLanguage";

i18next.use(initReactI18next).init({
  lng: getInitLanguage(),
  fallbackLng: "en",
  interpolation: {
    escapeValue: false,
  },
  resources: {
    en: {
      translation: lang_en,
    },
    es: {
      translation: lang_es,
    },
  },
});

export default i18next;
