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
}

export default function Tabs({
    tabs,
    hasHeader = true,
    hiddenBack = false,
}: Props) {
    const [step, setStep] = useState(0);
    const router = useRouter();
    const nextStep = (customStep?: number) => {
        if (step < tabs.length - 1) setStep(customStep || step + 1);
    };
    const backStep = (customStep?: number) => {
        if (step > 0) setStep(customStep || step - 1);
    };

    const initialStep = () => setStep(0);

    const renderComponent = () => {
        const Component = tabs[step].Component;

        return (
            <Component
                nextStep={nextStep}
                backStep={backStep}
                initialStep={initialStep}
            />
        );
    };

    return (
        <div className="mx-0 mx-md-4 mt-4 w-full">
            {hasHeader && (
                <div className="border-b-2 mt-2  border-[#E8E8E8] relative p-4 mx-4">
                    {!hiddenBack && (
                        <div className=" flex justify-center md:absolute items-center top-0 h-full ">
                            <MdKeyboardArrowLeft
                                size={30}
                                className=""
                                color={"#FC6700"}
                            />
                            <p
                                style={{ cursor: "pointer" }}
                                className="m-0  display-none md:block text-[18px]  text-[#FC6700]"
                                onClick={() => router.push("/")}
                            >
                                Volver al inicio
                            </p>
                        </div>
                    )}
                    <ul className=" flex flex-column flex-wrap lg:flex-row  list-unstyled  items-center justify-center md:px-[140px] ">
                        {tabs.map((tab, index) => (
                            <li
                                key={index}
                                className={`mx-2 opacity-75  ${
                                    index < step ||
                                    (tab.activeDefaultTab && index === step)
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
