// authStore.ts

import {
  addRecipeMedicament,
  createRecipe,
  getRecipeSign,
  sendRecipeByEmail,
  sendRecipeByWhatsapp,
  uploadRecipeFile,
} from "@/services/recipes";
import { IMedicament } from "@/types/Models/Medicament";
import { IRecipes } from "@/types/Models/Recipes";
import toast from "react-hot-toast";
import { create } from "zustand";
import { usePacients } from "./pacients";

type IState = {
  loading: any;
  error: string | null;
  recipe: any;
  recipes: any[];
  enableDownload: boolean;
  loadingDownload: boolean;
  emailLoading: boolean;
  hasDownload: boolean;
  hasMissingSign: boolean;
  CreateRecipe: (recipePayload: IRecipes, medicaments: IMedicament[]) => any;
  SendRecipeByWhatsapp: (recipeId?: string) => void;
  SendRecipeByEmail: (recipeId?: string) => void;
  ClearRecipe: () => void;
  handlePrint: (recipeId?: string, documentId?: string) => void;
  setEnableDownload: (value: boolean) => void;
  handleUploadDocument: (
    recipeId: string,
    documentId: string,
    fileBase64: String
  ) => void;
};

const baseUrl = process.env.NEXT_PUBLIC_BASE_URL;

export const useRecipeStore = create<IState>((set, get) => ({
  // Estado inicial
  loading: false,
  loadingDownload: false,
  error: null,
  recipe: "",
  recipes: [],
  emailLoading: false,
  hasDownload: false,
  enableDownload: false,
  hasMissingSign: false,

  ClearRecipe: () => {
    try {
      const { GetPacients } = usePacients.getState();

      set({ recipe: "" });

      GetPacients();
    } catch (error) {
      console.log("ERROR", error);
    }
  },

  // Accion de update
  CreateRecipe: async (recipePayload: IRecipes, medicaments: IMedicament[]) => {
    set({ loading: true, error: null, hasDownload: false });

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
        pressure: String(recipePayload.pressure),
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
      const result = [];

      let missingSign = true;

      set({
        hasMissingSign: missingSign,
      });

      if (groupMedicaments.length > 0) {
        missingSign = false;

        set({
          hasMissingSign: false,
        });

        const recipe = await createRecipe({
          ...recipePayloadTemp,
          medicaments: groupMedicaments,
          add_med: "[]",
        });

        result.push(recipe);
      }

      if (groupIIMedicaments.length > 0) {
        const recipe = await createRecipe({
          ...recipePayloadTemp,
          medicaments: groupIIMedicaments,
          add_med: "[]",
        });

        result.push(recipe);
      }

      if (groupIIIMedicaments.length > 0) {
        const recipe = await createRecipe({
          ...recipePayloadTemp,
          medicaments: groupIIIMedicaments,
          add_med: "[]",
        });

        result.push(recipe);
      }

      if (groupNewMedicaments.length > 0) {
        const recipe = await createRecipe({
          ...recipePayloadTemp,
          add_med: JSON.stringify(groupNewMedicaments),
        });

        result.push(recipe);
      }

      const recipePromises = result.map(async (recipe) => {
        const { data } = recipe.data;

        const groupType =
          (data.medicaments[0] && data.medicaments[0].group) ||
          "Medicamentos fuera de catálogo";

        if (
          groupType !== "Grupo II" &&
          groupType !== "Grupo III" &&
          data.add_med === "[]"
        ) {
          const sign = await getRecipeSign(recipe.data.data.id);
          return {
            ...recipe.data.data,
            sign: sign?.data,
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

  SendRecipeByWhatsapp: async (id?: string) => {
    set({ emailLoading: true, error: null });

    try {
      await sendRecipeByWhatsapp(id || "");

      toast.success("Receta enviada por Whatsapp");
      return true;
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message });
    } finally {
      set({ emailLoading: false, hasDownload: true });
    }
  },

  SendRecipeByEmail: async (recipeId?: string) => {
    set({ emailLoading: true, error: null });

    try {
      const recipe = get().recipe;

      await sendRecipeByEmail(recipeId || recipe?.data?.id);

      toast.success("Receta enviada por Email");
      return true;
    } catch (error: any) {
      toast(
        "Favor de intentarlo nuevamente presionando el botón Enviar por correo",
        {
          icon: "⚠️",
          style: {
            border: "1px solid #ffa502",
            padding: "16px",
            color: "#ffa502",
          },
        }
      );
      set({ error: error.message, emailLoading: false });
    } finally {
      set({ emailLoading: false, hasDownload: true });
    }
  },

  handlePrint: async (recipeId?: string, documentIds?: string) => {
    try {
      const recipe = get().recipe;
      set({ error: null, loadingDownload: true });
      const token: string | null = await localStorage.getItem("sessionToken");

      const idsToPrint = documentIds
        ? documentIds.split(";")
        : [recipe.data.id];

      for (const docId of idsToPrint) {
        const response = await fetch(
          `${baseUrl}/api/prescription/${recipeId || recipe.data.id}/file${
            docId ? `?document_id=${docId}` : ""
          }`,
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
      }

      set({ loadingDownload: false, error: null, hasDownload: true });
    } catch (err) {
      set({ loadingDownload: false });
      toast(
        "Favor de intentarlo nuevamente presionando el botón Imprimir/Visualizar PDF",
        {
          icon: "⚠️",
          style: {
            border: "1px solid #ffa502",
            padding: "16px",
            color: "#ffa502",
          },
        }
      );
      console.error("Error al obtener el PDF:", err);
    }
  },

  handleUploadDocument: async (
    recipeId: string,
    documentId: string,
    fileBase64: String
  ) => {
    try {
      await uploadRecipeFile(recipeId, {
        document_id: documentId,
        file: fileBase64,
      });
    } catch (error) {
      console.log("error", error);
    }
  },
}));
