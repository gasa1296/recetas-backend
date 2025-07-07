import React from "react";
import { useState } from "react";
import RegisterForm from "@/components/register/RegisterForm";
import { RegisterLayout } from "@/components/register/RegisterLayout";
import { RegistrationCard } from "@/components/register/RegistrationCard";
import { HelpAndHoursSection } from "@/components/register/HelpAndHoursSection";
import { BenefitsBanner } from "@/components/register/BenefitsBanner";
import { WhatsAppButton } from "@/components/register/WhatsAppButton";
import RegisterSuccess from "@/components/register/RegisterSuccess";
export default function RegisterPage() {
  const [successScreen, setSuccessScreen] = useState(false);

  return (
    <RegisterLayout>
      {/* Login section */}
      {!successScreen ? (
        <div className="col-span-12 mx-auto">
          <div className="grid grid-cols-1 md:grid-cols-5 gap-6 max-w-5xl mx-auto">
            <div className="col-span-1 md:col-span-3 flex justify-center">
              <RegisterForm setSuccessScreen={setSuccessScreen} />
            </div>
            <div className="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-1 gap-6 ">
              <HelpAndHoursSection />
            </div>
          </div>
        </div>
      ) : (
        <>
          {/* Right side with registration and info cards */}
          <div className="col-span-12 mx-auto ">
            {/* Main grid for right side */}
            <div className="grid grid-cols-1 md:grid-cols-5 gap-6 max-w-5xl mx-auto">
              <div className="col-span-1 md:col-span-3 flex justify-center">
                <RegisterSuccess />
              </div>

              <div className="col-span-1 md:col-span-2 grid grid-cols-1 md:grid-cols-1 gap-6 ">
                <HelpAndHoursSection />
              </div>
            </div>
          </div>
        </>
      )}

      <BenefitsBanner />
      <WhatsAppButton />
    </RegisterLayout>
  );
}
