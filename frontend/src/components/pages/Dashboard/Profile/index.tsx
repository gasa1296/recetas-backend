import { useRouter } from "next/router";
import React, { useEffect } from "react";
import { MdOutlineArrowBackIos } from "react-icons/md";
import TabsProfile from "./TabProfile";
import PersonalInformation from "./TabProfile/PersonalInformation";
import { useRegisterStore } from "@/store/register";
import Offices from "./TabProfile/Offices";
import ProfessionalDataProfile from "./TabProfile/ProfessionalDataProfile";
import { FaRegUser } from "react-icons/fa";

export default function Profile() {
  const router = useRouter();
  const setClearForms = useRegisterStore((state) => state.setClearForms);
  const tabs = [
    { label: "Datos personales", Component: PersonalInformation },
    { label: "Datos profesionales", Component: ProfessionalDataProfile },
    { label: "Consultorios", Component: Offices },
  ];
  useEffect(() => {
    return () => setClearForms();
  }, []);
  return (
    <section className="px-3 md:px-12  mt-8 ">
      {/* <div className=' container-dashboard'>
                <button onClick={() => router.push(`/dashboard`)} className='button-BlacK flex justify-center items-center p-2 w-[150px] '><MdOutlineArrowBackIos size={20} /> <p className='ms-1'>  Volver al inicio</p>    </button>
            </div> */}
      <div className="flex items-center  border-Tab p-2 ps-3">
        <FaRegUser color="#Fff " size={28} />
        <p className="text-[#fff] text-[26px] ms-3">Médico</p>
      </div>
      <section className="flex bg-[#fff]   ">
        <TabsProfile tabs={tabs} hiddenBack />
      </section>
    </section>
  );
}
