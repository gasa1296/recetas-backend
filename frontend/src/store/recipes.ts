// authStore.ts

import {
  addRecipeMedicament,
  createRecipe,
  getRecipeSign,
} from "@/services/recipes";
import { IMedicament } from "@/types/Models/Medicament";
import { IRecipes } from "@/types/Models/Recipes";
import toast from "react-hot-toast";
import { create } from "zustand";

type IState = {
  loading: any;
  error: string | null;
  recipe: any;

  CreateRecipe: (recipePayload: IRecipes, medicaments: IMedicament[]) => any;
};

export const useRecipeStore = create<IState>((set, get) => ({
  // Estado inicial
  loading: false,
  error: null,
  recipe: "",

  // Accion de update
  CreateRecipe: async (recipePayload: IRecipes, medicaments: IMedicament[]) => {
    set({ loading: true, error: null });

    try {
      const filterNewMedicaments = medicaments.filter(
        (medicament) => medicament.new
      );

      recipePayload.height = Number(recipePayload.height);
      recipePayload.ppm = Number(recipePayload.ppm);
      recipePayload.saturation = Number(recipePayload.saturation);
      recipePayload.temp = Number(recipePayload.temp);
      recipePayload.weight = Number(recipePayload.weight);
      recipePayload.add_med = filterNewMedicaments;
      const result = await createRecipe(recipePayload);

      const filterMedicaments = medicaments.filter(
        (medicament) => !medicament.new
      );

      const resultMedicament = await addRecipeMedicament(
        result.data.data.id,
        filterMedicaments.map((medicament) => ({
          ...medicament,
          medicament_id: medicament.uicodproducto,
          family: medicament.familia,
          group: medicament.clasificacionsa,
          name: medicament.vnombreproducto || "",
          type: medicament.tipoproducto,
        }))
      );

      const ResultSign = await getRecipeSign(result.data.data.id);

      console.log("first", ResultSign);

      set({
        recipe: result.data,
      });

      toast.success("Receta precreada correctamente");
      return true;
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },
}));
