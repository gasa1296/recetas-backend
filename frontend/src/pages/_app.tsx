import "@/styles/globals.css";
import type { AppProps } from "next/app";

import "@/languages/i18n";
import AuthWrapper from "@/components/AuthWrapper";
import { Toaster } from "react-hot-toast";

export default function App({ Component, pageProps }: AppProps) {
    return (
        <AuthWrapper>
            <>
                <Toaster />
                <Component {...pageProps} />
            </>
        </AuthWrapper>
    );
}
