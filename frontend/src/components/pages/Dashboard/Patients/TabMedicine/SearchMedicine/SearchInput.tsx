import React from "react";
import Select from "react-select";
import { colourStyles } from "../../helper";
import { useMedicamentStore } from "@/store/medicaments";
import MedicineNotFound from "./MedicineNotFound";
import { FaSearch } from "react-icons/fa";
export default function SearchInput() {
  const {
    loading,
    selectedMedicamentDefault,
    SearchMedicaments,
    SelectMedicament,
    search,
    SetSearch,
  } = useMedicamentStore((state) => ({
    loading: state.loading,
    selectedMedicamentDefault: state.selectedMedicamentDefault,
    SearchMedicaments: state.SearchMedicaments,
    SelectMedicament: state.SelectMedicament,
    search: state.search,
    SetSearch: state.SetSearch,
  }));

  const medicineOptions: any = [];

  return (
    <div className="search-input">
      <Select
        placeholder="Buscar medicamento por Nombre, Principio activo o dispositivo médico"
        className=""
        defaultValue={selectedMedicamentDefault}
        value={selectedMedicamentDefault}
        isSearchable={true}
        name="color"
        inputValue={search}
        options={medicineOptions || []}
        styles={colourStyles}
        isLoading={loading}
        noOptionsMessage={() => null}
        onInputChange={(value, action) => {
          if (value) SearchMedicaments(value);
          if (action.action === "input-change") SetSearch(value);

          return value;
        }}
        onChange={(value: any) => SelectMedicament(value.value)}
      />
    </div>
  );
}
