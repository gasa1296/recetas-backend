import React, { useEffect } from "react";
import { FaRegUser } from "react-icons/fa";
import Select from "react-select";
import { usePacients } from "@/store/pacients";
import useCustomEffect from "@/hooks/useCustomEffect";
import { colourStyles } from "../../helper";
import Loading from "@/components/Loading";
import PacientsNotFound from "./Component/PacientsNotFound";
import FindPacient from "./Component/FindPacient";
import SelectedUser from "./Component/SelectedUser";
import CreateUser from "./Component/CreateUser";
import RecipesData from "../RecipesData";
export default function InformationPatient({ nextStep }: any) {
  const {
    GetPacients,
    pacients,
    SelectPacient,
    selectedPacientDefault,
    loadingAction,
    loading,
    SearchPacients,
    step,
  } = usePacients((state) => ({
    GetPacients: state.GetPacients,
    SelectPacient: state.SelectPacient,
    pacients: state.pacients,
    loading: state.loading,
    SearchPacients: state.SearchPacients,
    loadingAction: state.loadingAction,
    selectedPacientDefault: state.selectedPacientDefault,
    step: state.step,
  }));

  useCustomEffect({ requestGet: GetPacients });

  const userOptions = pacients
    ?.map((pacient) => ({
      value: pacient.id,
      label: `${pacient.last_name1} ${pacient.last_name2}, ${pacient.first_name} | ${pacient.email} `,
    }))
    .sort((a, b) => {
      const labelA = a.label.trim().toLowerCase();
      const labelB = b.label.trim().toLowerCase();
      if (labelA < labelB) return -1;
      if (labelA > labelB) return 1;
      return 0;
    });

  const screen: any = {
    1: FindPacient,
    2: SelectedUser,
    3: CreateUser,
    4: RecipesData,
  };
  const Component = screen[step];

  return (
    <section>
      {step === 4 || step === 3 ? (
        <Component nextStep={nextStep} />
      ) : (
        <>
          <div className="flex items-center  border-Tab p-2 ps-3">
            <FaRegUser color="#Fff " size={28} />
            <p className="text-[#fff] text-[26px] ms-3">Paciente</p>
          </div>
          <section className="container-Patiens  py-5 flex justify-center  ">
            <div className="w-full px-8">
              <Select
                placeholder="Buscar paciente por Nombre, Apellido, Correo, Teléfono"
                className=""
                defaultValue={selectedPacientDefault}
                value={""}
                isSearchable={true}
                name="color"
                options={userOptions || []}
                styles={colourStyles}
                isLoading={loading}
                noOptionsMessage={() => <PacientsNotFound />}
                onInputChange={(value, actionMeta) => {
                  if (
                    value ||
                    (value === "" && actionMeta.action === "input-change")
                  )
                    SearchPacients(value);
                  return value;
                }}
                onChange={(value: any) => {
                  SelectPacient(value.value);
                  GetPacients();
                }}
                filterOption={(option, inputValue) => {
                  const { label, value } = option;
                  const pacient = pacients?.find((p) => p.id === value);
                  if (!pacient) return false;

                  const searchableFields = [
                    pacient.first_name,
                    pacient.last_name1,
                    pacient.last_name2,
                    pacient.email,
                    typeof pacient.phone1 === "string"
                      ? pacient.phone1.startsWith("[")
                        ? JSON.parse(pacient.phone1)
                        : pacient.phone1
                      : pacient.phone1 || [],
                  ]
                    .flat()
                    .filter(Boolean)
                    .join(" ")
                    .toLowerCase();

                  return searchableFields.includes(inputValue.toLowerCase());
                }}
              />

              {loadingAction ? <Loading /> : <Component nextStep={nextStep} />}
            </div>
          </section>
        </>
      )}
    </section>
  );
}
