import Image from "next/image";
import Link from "next/link";
import { ChevronRight } from "lucide-react";
import Factura2 from "@/assets/login/Factura-2.png";
import Wrapper from "@/assets/login/Wrapper.png";

export const RegistrationCard = () => {
  return (
    <div className="col-span-6 xl:col-span-4 bg-[#FAE0BD33] p-4 rounded-lg">
      <div className="bg-[white] rounded-lg overflow-hidden shadow-sm">
        <div className="bg-gradient-to-tr  from-[#0091FF] to-[#0041B8] h-[240px] relative">
          <Image
            src={Factura2}
            alt="Medical equipment"
            width={460}
            height={251}
            className=" h-[240px] w-auto object-contain absolute top-10 left-0 right-0 mx-auto"
            unoptimized
          />
        </div>
        <div className="p-6 pt-20">
          <div className="flex items-center gap-2 text-[#847FCB] font-bold text-xl mb-4">
            <Image
              src={Wrapper}
              alt="Medical equipment"
              width={24}
              height={24}
              className=" h-7 w-7"
              unoptimized
            />
            Ingresa con tu cuenta de médico
          </div>
          <p className="text-gray-700 mb-4">
            Accede a tu cuenta para gestionar tu práctica médica de manera
            eficiente.
          </p>
          <Link
            href="/"
            className="text-[#847FCB] font-medium flex items-center hover:underline"
          >
            Iniciar sesión
            <ChevronRight className="h-5 w-5 ml-1" />
          </Link>
        </div>
      </div>
    </div>
  );
};
