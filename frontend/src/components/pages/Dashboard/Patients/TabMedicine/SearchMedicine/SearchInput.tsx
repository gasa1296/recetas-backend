import React from "react";
import Select from "react-select";
import { colourStyles } from "../../helper";
import { useMedicamentStore } from "@/store/medicaments";
import MedicineNotFound from "./MedicineNotFound";
export default function SearchInput() {
    const {
        loading,
        selectedMedicamentDefault,
        SearchMedicaments,
        SelectMedicament,
    } = useMedicamentStore((state) => ({
        loading: state.loading,
        selectedMedicamentDefault: state.selectedMedicamentDefault,
        SearchMedicaments: state.SearchMedicaments,
        SelectMedicament: state.SelectMedicament,
    }));

    const medicineOptions: any = [];
    return (
        <Select
            placeholder="Buscar paciente por Nombre, Apellido, Correo, Teléfono, Folio de receta"
            className=""
            defaultValue={selectedMedicamentDefault}
            value={selectedMedicamentDefault}
            isSearchable={true}
            name="color"
            options={medicineOptions || []}
            styles={colourStyles}
            isLoading={loading}
            noOptionsMessage={() => null}
            onInputChange={(value) => {
                if (value) SearchMedicaments(value);

                return value;
            }}
            onChange={(value: any) => SelectMedicament(value.value)}
        />
    );
}
