import useCustomEffect from "@/hooks/useCustomEffect";
import { useMedicamentStore } from "@/store/medicaments";
import { ResultMedicineItem } from "../TabMedicine/SearchMedicine/Components/ResultMedicineItem";

export function PopularMedicaments() {
  const GetPopularMedicaments = useMedicamentStore(
    (state) => state.GetPopularMedicaments
  );
  const popularMedicaments = useMedicamentStore(
    (state) => state.popularMedicaments
  );

  const medicines = popularMedicaments?.map((medicine) => ({
    presentacion: `https://s3-repositorio-cloudseus.s3.amazonaws.com/commerce/products/product_${medicine.uicodproducto}.png`,
    name: medicine.vnombreproducto,
    information: medicine.vnombresal?.split(";").join(" "),
    uuid: medicine.uicodproducto,
  }));

  /*   useCustomEffect({ requestGet: GetPopularMedicaments }); */

  if (!medicines.length) return null;

  return (
    <section className="mt-6  relative">
      <p className="text-[#4B4B4B] text-[16px] font-bold mt-3">
        Medicamentos favoritos
      </p>
      <p className="text-[#4B4B4B] text-[16px]  mt-2">
        Medicamentos que ha agregado a sus recetas frecuentemente
      </p>
      <div className="flex flex-wrap justify-start mt-4 ">
        {medicines?.map((medicine: any) => {
          return <ResultMedicineItem key={medicine.uuid} medicine={medicine} />;
        })}
      </div>
    </section>
  );
}
