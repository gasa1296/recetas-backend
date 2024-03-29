import { useMedicamentStore } from "@/store/medicaments";
import Image from "next/image";
import MedicineDefault from "@/assets/images/placeholder.jpg";
import { useState } from "react";

interface Props {
  medicine: any;
}

export function ResultMedicineItem({ medicine }: Props) {
  const { SelectMedicament, SetSearch } = useMedicamentStore((state) => ({
    SelectMedicament: state.SelectMedicament,

    SetSearch: state.SetSearch,
  }));

  const [error, setError] = useState(false);

  return (
    <div key={medicine.uuid} className="card-medicine mt-2 relative mx-2">
      {error ? (
        <Image
          src={MedicineDefault}
          alt="Picture"
          width={500}
          height={500}
          className="image-medicament"
        />
      ) : (
        <Image
          src={medicine.presentacion}
          alt="Picture"
          width={500}
          height={500}
          className="image-medicament"
          onError={({ currentTarget }) => {
            setError(true);
          }}
        />
      )}

      <p className="text-[#4B4B4B] font-semibold text-[14px] ms-4 mt-4">
        {medicine.name}
      </p>

      <p className="text-[#141414] text-[14px] ms-4">
        {medicine.information.slice(0, 180)}
        {medicine.information.length > 180 ? "..." : ""}
      </p>
      <div className="flex justify-center absolute w-full bottom-5">
        <button
          onClick={() => {
            SelectMedicament(medicine.uuid);
            SetSearch("");
          }}
          className="button-BlacK2 p-1 w-[80%] rounded-full mt-7"
        >
          Seleccionar
        </button>
      </div>
    </div>
  );
}
