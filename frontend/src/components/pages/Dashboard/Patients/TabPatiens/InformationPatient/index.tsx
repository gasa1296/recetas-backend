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
import useRecipeNext from "@/hooks/useRecipeNext";
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

    useRecipeNext({ nextStep });
    useCustomEffect({ requestGet: GetPacients });

    const userOptions = pacients?.map((pacient) => ({
        value: pacient.email,
        label: `${pacient.last_name1} ${pacient.last_name2}, ${pacient.first_name} | ${pacient.email}`,
    }));

    const screen: any = {
        1: FindPacient,
        2: SelectedUser,
        3: CreateUser,
    };

    const Component = screen[step];

    return (
        <section>
            <div className="flex items-center  border-Tab p-2 ps-3">
                <FaRegUser color="#Fff " size={28} />
                <p className="text-[#fff] text-[26px] ms-3">Paciente</p>
            </div>
            <section className="container-Patiens  py-5 flex justify-center  ">
                <div className="w-[100%] px-8">
                    <Select
                        placeholder="Buscar paciente por Nombre, Apellido, Correo, Teléfono, Folio de receta"
                        className=""
                        defaultValue={selectedPacientDefault}
                        value={selectedPacientDefault}
                        isSearchable={true}
                        name="color"
                        options={userOptions || []}
                        styles={colourStyles}
                        isLoading={loading}
                        noOptionsMessage={() => <PacientsNotFound />}
                        onInputChange={(value) => {
                            if (value) SearchPacients(value);
                            return value;
                        }}
                        onChange={(value: any) => SelectPacient(value.value)}
                    />

                    {loadingAction ? (
                        <Loading />
                    ) : (
                        <Component nextStep={nextStep} />
                    )}
                </div>
            </section>
        </section>
    );
}
