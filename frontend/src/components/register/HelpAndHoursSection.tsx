import React from "react";
import Link from "next/link";
import { ChevronRight } from "lucide-react";
import Image from "next/image";
import CallPhone from "@/assets/login/call-phone.png";
import Clock from "@/assets/login/clock.png";
export const HelpAndHoursSection = () => {
  return (
    <div className="flex flex-row md:flex-col col-span-6  xl:col-span-2">
      <div className="bg-white rounded-lg p-5 py-6 shadow-sm text-center w-full ">
        <div className="flex justify-center mb-2">
          <div>
            <Image
              src={CallPhone}
              alt="Phone icon"
              width={80}
              height={80}
              className="h-24 w-24 object-contain"
              unoptimized
            />
          </div>
        </div>
        <h3 className="font-medium text-black text-md">¿Necesitas ayuda?</h3>
        <p className="text-gray-600 text-sm mb-1">Comunícate al</p>
        <Link
          href="tel:5552784540"
          className="text-[#847FCB] font-bold text-lg flex items-center justify-center"
        >
          55 5278 4540
          <ChevronRight className="h-5 w-5 ml-1" />
        </Link>
      </div>

      {/* Hours card - right column on desktop */}

      <div className="bg-white rounded-lg p-5 py-6 shadow-sm text-center ml-4 md:ml-0 w-full md:mt-4">
        <div className="flex justify-center mb-2">
          <div>
            <Image
              src={Clock}
              alt="Phone icon"
              width={100}
              height={100}
              className="h-24 w-24 object-contain"
              unoptimized
            />
          </div>
        </div>
        <h3 className="font-medium text-black mb-2 text-md">
          Horario de Atención:
        </h3>
        <div className="space-y-1 text-md">
          <p>
            <span className="font-bold text-[#847FCB]">Lun-Vie:</span> 8:00 -
            21:00 hrs.
          </p>
          <p>
            <span className="font-bold text-[#847FCB]">Sáb:</span> 8:00 am
            -19:00 hrs.
          </p>
          <p>
            <span className="font-bold text-[#847FCB]">Dom:</span> 9:00 am –
            2:00 pm
          </p>
        </div>
      </div>
    </div>
  );
};
