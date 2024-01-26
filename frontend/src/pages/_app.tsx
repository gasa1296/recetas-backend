import "@/styles/globals.css";
import type { AppProps } from "next/app";

import "@/languages/i18n";
import AuthWrapper from "@/components/AuthWrapper";
import { Toaster } from "react-hot-toast";
import Head from "next/head";

export default function App({ Component, pageProps }: AppProps) {
  return (
    <AuthWrapper>
      <>
        <Head>
          <title>Receta médica FESA</title>
        </Head>
        <Toaster />
        <Component {...pageProps} />
      </>
    </AuthWrapper>
  );
}
