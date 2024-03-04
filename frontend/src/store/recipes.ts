// authStore.ts

import {
  addRecipeMedicament,
  createRecipe,
  getRecipeSign,
  sendRecipeByEmail,
  sendRecipeByWhatsapp,
} from "@/services/recipes";
import { IMedicament } from "@/types/Models/Medicament";
import { IRecipes } from "@/types/Models/Recipes";
import toast from "react-hot-toast";
import { create } from "zustand";

type IState = {
  loading: any;
  error: string | null;
  recipe: any;
  enableDownload: boolean;
  hasMissingSign: boolean;
  CreateRecipe: (recipePayload: IRecipes, medicaments: IMedicament[]) => any;
  SendRecipeByWhatsapp: () => void;
  SendRecipeByEmail: () => void;
  ClearRecipe: () => void;
  handlePrint: () => void;
  setEnableDownload: (value: boolean) => void;
};

const baseUrl = process.env.NEXT_PUBLIC_BASE_URL;

export const useRecipeStore = create<IState>((set, get) => ({
  // Estado inicial
  loading: false,
  error: null,
  recipe: "",
  enableDownload: false,
  hasMissingSign: false,

  ClearRecipe: () => {
    set({ recipe: "" });
  },

  // Accion de update
  CreateRecipe: async (recipePayload: IRecipes, medicaments: IMedicament[]) => {
    set({ loading: true, error: null });

    try {
      const filterNewMedicaments = medicaments.filter(
        (medicament) => medicament.new
      );

      const filterMedicaments = medicaments.filter(
        (medicament) => !medicament.new
      );

      recipePayload.height = Number(recipePayload.height);
      recipePayload.ppm = Number(recipePayload.ppm);
      recipePayload.saturation = Number(recipePayload.saturation);
      recipePayload.temp = Number(recipePayload.temp);
      recipePayload.weight = Number(recipePayload.weight);
      recipePayload.add_med = JSON.stringify(filterNewMedicaments);
      recipePayload.medicaments = filterMedicaments.map((medicament) => ({
        ...medicament,
        medicament_id: medicament.uicodproducto,
        family: medicament.familia,
        group: medicament.clasificacionsa,
        name: medicament.vnombreproducto || "",
        type: medicament.tipoproducto,
        salt: medicament.vnombresal,
      }));
      const result = await createRecipe(recipePayload);

      let findGroupMedicamente: any = filterMedicaments.filter(
        (medicament) =>
          medicament.clasificacionsa === "Grupo II" ||
          medicament.clasificacionsa === "Grupo III"
      );

      let missingSign =
        findGroupMedicamente?.length > 0 || filterNewMedicaments.length > 0
          ? true
          : false;

      let ResultSign = null;
      if (!missingSign) {
        ResultSign = await getRecipeSign(result.data.data.id);
      }

      set({
        recipe: {
          ...result.data,
          signer: ResultSign?.data.data.signers[0].id || "",
        },
        enableDownload: false,
        hasMissingSign: missingSign,
      });

      toast.success("Receta precreada correctamente");
      return { missingSign };
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  setEnableDownload: (value: boolean) => {
    set({ enableDownload: value });
  },

  SendRecipeByWhatsapp: async () => {
    set({ loading: true, error: null });

    try {
      const recipe = get().recipe;

      await sendRecipeByWhatsapp(recipe?.data?.id);

      toast.success("Receta enviada por Whatsapp");
      return true;
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  SendRecipeByEmail: async () => {
    set({ loading: true, error: null });

    try {
      const recipe = get().recipe;

      await sendRecipeByEmail(recipe?.data?.id);

      toast.success("Receta enviada por Email");
      return true;
    } catch (error: any) {
      toast.error("Error al descargar la receta, aun no esta lista");
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  handlePrint: async () => {
    try {
      const recipe = get().recipe;
      set({ loading: true, error: null });
      const token: string | null = await localStorage.getItem("sessionToken");
      const response = await fetch(
        `${baseUrl}/api/prescription/${recipe.data.id}/file`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
          },
        }
      );

      if (!response.ok) {
        throw new Error(`HTTP error: ${response.status}`);
      }

      const blob = new Blob([await response.blob()], {
        type: "application/pdf",
      });

      const blobUrl = URL.createObjectURL(blob);

      window.open(blobUrl, "_blank");
      set({ loading: false, error: null });
    } catch (err) {
      set({ loading: false });
      toast.error("Error al descargar la receta, aun no esta lista");
      console.error("Error al obtener el PDF:", err);
    }
  },
}));
