import DashboardLayout from "@/components/Layouts/DashboardLayout";
import Patients from "@/components/pages/Dashboard/Patients";
import Head from "next/head";
import React from "react";

export default function PatiensPage() {
  const sdkUrl =
    process.env.NEXT_PUBLIC_LEGALARIO_ENVIRONMENT === "SANDBOX"
      ? "https://sdk.legalario.com/3.4/sdk-dev.js"
      : "https://sdk.legalario.com/3.4/sdk-dist.js";
  return (
    <DashboardLayout>
      <Head>
        <script type="module" src={sdkUrl}></script>
      </Head>
      <Patients />
    </DashboardLayout>
  );
}
