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
  recipes: any[];
  enableDownload: boolean;
  hasMissingSign: boolean;
  CreateRecipe: (recipePayload: IRecipes, medicaments: IMedicament[]) => any;
  SendRecipeByWhatsapp: (recipeId?: string) => void;
  SendRecipeByEmail: (recipeId?: string) => void;
  ClearRecipe: () => void;
  handlePrint: (recipeId?: string) => void;
  setEnableDownload: (value: boolean) => void;
};

const baseUrl = process.env.NEXT_PUBLIC_BASE_URL;

export const useRecipeStore = create<IState>((set, get) => ({
  // Estado inicial
  loading: false,
  error: null,
  recipe: "",
  recipes: [],
  enableDownload: false,
  hasMissingSign: false,

  ClearRecipe: () => {
    set({ recipe: "" });
  },

  // Accion de update
  CreateRecipe: async (recipePayload: IRecipes, medicaments: IMedicament[]) => {
    set({ loading: true, error: null });

    try {
      const groupIIMedicaments: IMedicament[] = [];
      const groupIIIMedicaments: IMedicament[] = [];
      const groupNewMedicaments: IMedicament[] = [];
      const groupMedicaments: IMedicament[] = [];

      const recipePayloadTemp = {
        ...recipePayload,
        height: Number(recipePayload.height),
        ppm: Number(recipePayload.ppm),
        saturation: Number(recipePayload.saturation),
        temp: Number(recipePayload.temp),
        weight: Number(recipePayload.weight),
        medicaments: [],
      };

      medicaments.map((medicament) => {
        switch (medicament.group) {
          case "Grupo II":
            groupIIMedicaments.push(medicament);
            break;
          case "Grupo III":
            groupIIIMedicaments.push(medicament);
            break;
          default:
            if (medicament.new) {
              groupNewMedicaments.push(medicament);
            } else {
              groupMedicaments.push(medicament);
            }
            break;
        }
      });

      const recipesPetition = [];
      const result = [];
      let missingSign = true;

      if (groupMedicaments.length > 0) {
        missingSign = false;

        const recipe = await createRecipe({
          ...recipePayloadTemp,
          medicaments: groupMedicaments,
          add_med: "[]",
        });

        result.push(recipe);
        /* recipesPetition.push(
          createRecipe({
            ...recipePayloadTemp,
            medicaments: groupMedicaments,
            add_med: "[]",
          })
        ); */
      }

      if (groupIIMedicaments.length > 0) {
        const recipe = await createRecipe({
          ...recipePayloadTemp,
          medicaments: groupIIMedicaments,
          add_med: "[]",
        });

        result.push(recipe);
        /* recipesPetition.push(
          createRecipe({
            ...recipePayloadTemp,
            medicaments: groupIIMedicaments,
            add_med: "[]",
          })
        ); */
      }

      if (groupIIIMedicaments.length > 0) {
        const recipe = await createRecipe({
          ...recipePayloadTemp,
          medicaments: groupIIIMedicaments,
          add_med: "[]",
        });

        result.push(recipe);
        /*  recipesPetition.push(
          createRecipe({
            ...recipePayloadTemp,
            medicaments: groupIIIMedicaments,
            add_med: "[]",
          })
        ); */
      }

      if (groupNewMedicaments.length > 0) {
        const recipe = await createRecipe({
          ...recipePayloadTemp,
          add_med: JSON.stringify(groupNewMedicaments),
        });

        result.push(recipe);
        /* recipesPetition.push(
          createRecipe({
            ...recipePayloadTemp,
            add_med: JSON.stringify(groupNewMedicaments),
          })
        ); */
      }

      /* const result = await Promise.all(recipesPetition); */

      const recipePromises = result.map(async (recipe) => {
        const { data } = recipe.data;

        const groupType =
          (data.medicaments[0] && data.medicaments[0].group) ||
          "Nuevos medicamentos";

        if (
          groupType !== "Grupo II" &&
          groupType !== "Grupo III" &&
          data.add_med === "[]"
        ) {
          const sign = await getRecipeSign(recipe.data.data.id);
          return {
            ...recipe.data.data,
            sign: sign?.data.data.signers[0].id || "",
            hasSign: true,
            groupType: "Medicamentos generales",
          };
        }

        return { ...recipe.data.data, groupType };
      });

      const recipes = await Promise.all(recipePromises);

      set({
        recipes,
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

  SendRecipeByEmail: async (recipeId?: string) => {
    set({ loading: true, error: null });

    try {
      const recipe = get().recipe;

      await sendRecipeByEmail(recipeId || recipe?.data?.id);

      toast.success("Receta enviada por Email");
      return true;
    } catch (error: any) {
      toast(
        "Favor de intentarlo nuevamente presionando el botón Enviar por correo",
        {
          icon: "⚠️", // Icono unicode de advertencia (opcional)
          style: {
            border: "1px solid #ffa502", // Borde naranja
            padding: "16px", // Espaciado interno
            color: "#ffa502", // Color del texto naranja
          },
        }
      );
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  handlePrint: async (recipeId?: string) => {
    try {
      const recipe = get().recipe;
      set({ loading: true, error: null });
      const token: string | null = await localStorage.getItem("sessionToken");
      const response = await fetch(
        `${baseUrl}/api/prescription/${recipeId || recipe.data.id}/file`,
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
      toast(
        "Favor de intentarlo nuevamente presionando el botón Imprimir/Visualizar PDF",
        {
          icon: "⚠️", // Icono unicode de advertencia (opcional)
          style: {
            border: "1px solid #ffa502", // Borde naranja
            padding: "16px", // Espaciado interno
            color: "#ffa502", // Color del texto naranja
          },
        }
      );
      console.error("Error al obtener el PDF:", err);
    }
  },
}));
