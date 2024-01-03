import React, { useState } from "react";
import { FaCheck } from "react-icons/fa";
import { MdKeyboardArrowLeft } from "react-icons/md";

import { useRouter } from "next/router";

interface Props {
    tabs: {
        label: string;
        Component: any;
    }[];
    hasHeader?: boolean;
    hiddenBack?: boolean;
}

export default function TabsProfile({
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
                <div className="border-b-2 mt-2   border-[#E8E8E8] relative  ">
                    <ul className=" flex flex-column flex-wrap lg:flex-row  list-unstyled  items-center justify-start md:px-[15px] ">
                        {tabs.map((tab, index) => (
                            <li
                                onClick={() => setStep(index)}
                                key={index}
                                className={`opacity-75  p-2 text-[20px] font-bold  cursor-pointer ${
                                    index === step
                                        ? "text-[#FC6600] border border-b-0 "
                                        : "text-[#81828B]  border-none "
                                } text-[18px] ] flex items-center  `}
                            >
                                <p className="m-0 px-2">{tab.label}</p>
                            </li>
                        ))}
                    </ul>
                </div>
            )}

            <div className="px-10"> {renderComponent()}</div>
        </div>
    );
}
