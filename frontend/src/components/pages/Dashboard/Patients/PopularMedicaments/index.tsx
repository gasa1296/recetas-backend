import useCustomEffect from "@/hooks/useCustomEffect";
import { useMedicamentStore } from "@/store/medicaments";
import { ResultMedicineItem } from "../TabMedicine/SearchMedicine/Components/ResultMedicineItem";
import Loading from "@/components/Loading";

export function PopularMedicaments() {
  const GetPopularMedicaments = useMedicamentStore(
    (state) => state.GetPopularMedicaments
  );
  const popularMedicaments = useMedicamentStore(
    (state) => state.popularMedicaments
  );

  const loadingPopularMedicaments = useMedicamentStore(
    (state) => state.loadingPopularMedicaments
  );

  const medicines = popularMedicaments?.map((medicine) => ({
    presentacion: `https://s3-repositorio-cloudseus.s3.amazonaws.com/commerce/products/product_${medicine.uicodproducto}.png`,
    name: medicine.vnombreproducto,
    information: medicine.vnombresal?.split(";").join(" "),
    uuid: medicine.uicodproducto,
  }));

  useCustomEffect({ requestGet: GetPopularMedicaments });

  if (loadingPopularMedicaments)
    return (
      <section className="mt-4  relative">
        <Loading
          text="Cargando medicamentos preescritos con mayor frecuencia"
          textSize={30}
        />
      </section>
    );
  if (!medicines.length) return null;

  return (
    <section className="mt-6  relative">
      <p className="text-[#4B4B4B] text-[18px] font-bold mt-3">
        Medicamentos preescritos con mayor frecuencia
      </p>

      <div className="flex flex-wrap justify-start mt-4 ">
        {medicines?.map((medicine: any) => {
          return <ResultMedicineItem key={medicine.uuid} medicine={medicine} />;
        })}
      </div>
    </section>
  );
}
