import React, { useState } from "react";
import { FaCheck } from "react-icons/fa";
import { MdKeyboardArrowLeft } from "react-icons/md";

import { useRouter } from "next/router";

interface Props {
  tabs: {
    label: string;
    Component: any;
    activeDefaultTab?: boolean;
  }[];
  hasHeader?: boolean;
  hiddenBack?: boolean;
  customStep?: number;
  setCustomStep?: any;
  disableMargin?: boolean;
}

export default function Tabs({
  tabs,
  hasHeader = true,
  hiddenBack = false,
  customStep,
  setCustomStep,
  disableMargin,
}: Readonly<Props>) {
  const [step, setStep] = useState(0);

  const generalSetStep = setCustomStep || setStep;
  const generalStep = customStep || step;
  const router = useRouter();
  const nextStep = (customStep2?: number) => {
    if (generalStep < tabs.length - 1)
      generalSetStep(customStep2 || generalStep + 1);
  };
  const backStep = (customStep?: number) => {
    if (generalStep > 0) generalSetStep(customStep || generalStep - 1);
  };

  const resetTab = () => {
    generalSetStep(0);
  };

  const initialStep = () => generalSetStep(0);

  const renderComponent = () => {
    const Component = tabs[generalStep].Component;

    return (
      <Component
        nextStep={nextStep}
        backStep={backStep}
        resetTab={resetTab}
        initialStep={initialStep}
      />
    );
  };

  return (
    <div className={`mx-0 mx-md-4 ${disableMargin ? "mt-0" : "mt-4"}  w-full`}>
      {hasHeader && (
        <div className="border-b-2 mt-2  border-[#E8E8E8] relative p-4 mx-4">
          {!hiddenBack && (
            <div className=" flex justify-center md:absolute items-center top-0 h-full ">
              <MdKeyboardArrowLeft size={30} className="" color={"#FC6700"} />
              <button
                style={{ cursor: "pointer" }}
                className="m-0  display-none md:block text-[18px]  text-[#FC6700]"
                onClick={() => router.push("/")}
              >
                Volver al inicio
              </button>
            </div>
          )}
          <ul className=" flex flex-column flex-wrap lg:flex-row  list-unstyled  items-center justify-center md:px-[140px] ">
            {tabs.map((tab, index) => (
              <li
                key={`tab-${tab.label}`}
                className={`mx-2 opacity-75  ${
                  index < generalStep ||
                  (tab.activeDefaultTab && index === generalStep)
                    ? "text-[#00C851]  "
                    : "text-[#81828B] opacity-50  "
                } text-[18px] ] flex items-center  `}
              >
                <FaCheck size={17} />
                <p className="m-0 px-2">
                  0{index + 1}. {tab.label}
                </p>
              </li>
            ))}
          </ul>
        </div>
      )}

      {renderComponent()}
    </div>
  );
}
