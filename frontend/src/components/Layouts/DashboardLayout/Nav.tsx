import React, { useEffect, useState } from "react";
import { FaBars } from "react-icons/fa";

import Image from "next/image";
import logo from "../../../assets/LogoFESA.svg";
import { useRouter } from "next/router";
import { useAuthStore } from "@/store/auth";
import { FaTrash } from "react-icons/fa";
import { getDateFormat } from "@/utils/getDateFormat";
import { FaFilePrescription } from "react-icons/fa";
import { useMedicamentStore } from "@/store/medicaments";
import { usePacients } from "@/store/pacients";
import { useRecipeStore } from "@/store/recipes";
import LoadingModal from "@/components/Loading/LoadingModal";
import { getMedicamentByCode } from "@/services/medicaments";

export default function Nav({ setScreen }: any) {
  const { user } = useAuthStore((state) => ({
    user: state.user,
  }));

  const router = useRouter();
  const loading = useRecipeStore((state) => state.loading);
  const ClearRecipe = useRecipeStore((state) => state.ClearRecipe);
  const [time, setTime] = useState<any>("");

  const ResetPacients = usePacients((state) => state.ResetPacients);
  const SetTabStep = usePacients((state) => state.SetTabStep);
  const tabStep = usePacients((state) => state.tabStep);

  const { cardMedicament, ResetMedicaments } = useMedicamentStore((state) => ({
    ResetMedicaments: state.ResetMedicaments,
    cardMedicament: state.cardMedicament,
  }));
  const { formattedTime, correctedDate } = getDateFormat("");

  useEffect(() => {
    const interval = setInterval(() => {
      setTime(new Date());
    }, 1000);

    return () => {
      clearInterval(interval);
    };
  }, []);
  return (
    <nav className=" p-5 block md:flex justify-between  bg-[#FFFFFF] container-nav sticky top-0 z-[100]  ">
      {loading && <LoadingModal />}
      <div className=" absolute flex items-center justify-center top-6 right-14 md:hidden">
        <button
          onClick={() => {
            ResetPacients();
            ResetMedicaments();
            SetTabStep(0);
            ClearRecipe();
          }}
          disabled={cardMedicament.length ? false : true}
          className="flex justify-center items-center   py-1 disabled:opacity-50 relative top-[19px]"
        >
          <FaTrash size={26} color="bg-[#fff]" />
        </button>
        <div
          className="mx-4 relative top-[19px]"
          style={{ cursor: "pointer" }}
          onClick={() => cardMedicament.length && tabStep < 2 && SetTabStep(2)}
        >
          <div className=" content-card flex align-center justify-center bg-[#000] text-white w-[20px] h-[20px] text-[12px]  absolute top-[-5px] right-[-5px] leading-[20px]">
            {cardMedicament.length}
          </div>
          <FaFilePrescription size={32} color="bg-[#fff]" />
        </div>

        <div
          onClick={() => {
            setScreen(true);
          }}
        >
          <FaBars size={40} className=" absolute " />
        </div>
      </div>

      <section className=" justify-between items-center  md:flex ">
        <Image src={logo} alt="logo" className="w-[194px] me-9 " />
        <div className="mx-6 lg:mx-4  hidden md:justify-center items-center md:flex ">
          <div>
            <p className="text-[#1A1A1A] text-[24px] font-normal m-0">
              Bienvenido
            </p>
            <p className="text-[#1A1A1A] text-[20px] font-bold ">
              Dr. {user?.first_name} {user?.last_name1}
            </p>
          </div>

          <div className="mx-6 lg:mx-14">
            <p className="text-[#1A1A1A] text-[24px] font-bold">
              {formattedTime}
            </p>
            <p className="text-[#1A1A1A] text-[18px] font-normal">
              {correctedDate}
            </p>
          </div>
        </div>
      </section>
      <section className=" justify-between items-center  hidden md:flex ">
        <button
          onClick={(e) => {
            e.preventDefault();
            ResetPacients();
            ResetMedicaments();
            SetTabStep(0);
            ClearRecipe();
          }}
          disabled={cardMedicament.length ? false : true}
          className="flex justify-center items-center border border-1 border-[#000] px-3 py-1 disabled:opacity-50"
        >
          <FaTrash />
          <span className="pl-2"> Vaciar receta</span>
        </button>
        <div
          className="flex items-center mx-4 relative"
          style={{ cursor: "pointer" }}
          onClick={async () => {
            if (cardMedicament.length && tabStep < 2) {
              router.push("/dashboard");
              SetTabStep(2);
            }
          }}
        >
          <div className=" content-card flex align-center justify-center bg-[#000] text-white w-[20px] h-[20px] text-[12px]  absolute top-[-5px] right-[-5px] leading-[20px]">
            {cardMedicament.length}
          </div>
          <FaFilePrescription size={32} color="bg-[#fff]" />
        </div>
      </section>
    </nav>
  );
}
