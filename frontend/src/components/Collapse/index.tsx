import { Field } from "@/types/Generals/FormGenerator";
import { useState } from "react";
import { IoIosArrowDown, IoIosArrowUp } from "react-icons/io";
const Collapse = ({ title, children, form }: any) => {
  const [isOpen, setIsOpen] = useState(false);

  const ArrowIcon = isOpen ? IoIosArrowUp : IoIosArrowDown;

  return (
    <div className="w-full">
      <button
        className="flex justify-between items-center w-full px-4 py-2 text-left text-[#1A1A1A] text-[18px] bg-gray-100 hover:bg-gray-200 focus:outline-none"
        onClick={(e) => {
          e.preventDefault();
          setIsOpen(!isOpen);
        }}
      >
        <span className="flex flex-wrap items-center">
          <span className="pr-3">{title} </span>{" "}
          {form.map(
            ({ Icon }: Field) =>
              Icon && (
                <span className="pr-2">
                  <Icon size={25} />
                </span>
              )
          )}
        </span>

        <ArrowIcon className="w-5 h-5" />
      </button>
      <div
        className={`transition-all ${
          isOpen ? "max-h-96" : "max-h-0"
        } overflow-hidden`}
      >
        <div className="px-2 pt-4  flex flex-wrap justify-between w-full">
          {children}
        </div>
      </div>
    </div>
  );
};

export default Collapse;
