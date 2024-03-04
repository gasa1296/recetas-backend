import DashboardLayout from "@/components/Layouts/DashboardLayout";
import Patients from "@/components/pages/Dashboard/Patients";
import useCustomEffect from "@/hooks/useCustomEffect";
import { usePacients } from "@/store/pacients";
import Head from "next/head";
import React from "react";

export default function PatiensPage() {
  const { ResetPacients } = usePacients((state) => ({
    ResetPacients: state.ResetPacients,
  }));

  useCustomEffect({ requestGet: ResetPacients });

  return (
    <DashboardLayout>
      <Patients />
    </DashboardLayout>
  );
}
