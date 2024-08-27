// authStore.ts
import { PopularMedicaments } from "@/components/pages/Dashboard/Patients/PopularMedicaments";
import {
  GetPopularMedicament,
  createMedicament,
  getMedicamentByCode,
  getSearchExternalMedicament,
} from "@/services/medicaments";

import {
  IConfirmRecipForm,
  IMedicament,
  IMedicamentDefault,
  INewMedicament,
} from "@/types/Models/Medicament";
import { IRecipes } from "@/types/Models/Recipes";
import { canAddMedicamentByGroup } from "@/utils/getValidGroup";
import toast from "react-hot-toast";
import { create } from "zustand";

type IState = {
  timeId: any;
  loading: boolean;
  loadingAction: boolean;
  medicaments: IMedicament[] | null;
  selectedMedicament: IMedicament | null;
  cardMedicament: IMedicament[];
  selectedMedicamentDefault: IMedicamentDefault | null;
  error: string | null;
  step: number;
  search: string;
  popularMedicaments: IMedicament[] | [];
  loadingPopularMedicaments: boolean;
  confirmRecipForm: IConfirmRecipForm;
  GetPopularMedicaments: () => any;
  SearchMedicaments: (search: string) => any;
  SetPopularMedicaments: (popularMedicaments: IMedicament[]) => any;
  SetSearch: (search: string) => any;
  ResetMedicaments: () => any;
  SelectMedicament: (medicamentId: string) => any;
  SetStep: (step: number) => any;
  SetConfirmForm: (confirmRecipForm: IConfirmRecipForm) => any;
  SetAllCardMedicament: (medicaments: IMedicament[]) => any;
  DuplicateRecipe: (
    medicaments: IMedicament[],
    confirmRecipForm: IRecipes
  ) => any;
  CreateMedicament: (medicamentPayload: IMedicament) => any;
};

export const useMedicamentStore = create<IState>((set, get) => ({
  // Estado inicial
  timeId: null,
  medicaments: null,
  popularMedicaments: [],
  step: 1,
  loading: false,
  loadingPopularMedicaments: false,
  confirmRecipForm: {
    temp: "",
    weight: "",
    height: "",
    pressure: "",
    saturation: "",
    ppm: "",
    diagnostic: "",
    add: "",
    room_id: "",
  },
  loadingAction: false,
  error: null,
  cardMedicament: [],
  selectedMedicament: null,
  selectedMedicamentDefault: null,
  search: "",

  SetStep: (step: number) => {
    set({ step });
  },
  SetSearch: (search: string) => {
    set({ search });
  },

  SetConfirmForm: (confirmRecipForm: IConfirmRecipForm) => {
    set({ confirmRecipForm });
  },

  SetAllCardMedicament: (medicaments: IMedicament[]) => {
    set({ cardMedicament: medicaments });
  },

  SetPopularMedicaments: (popularMedicaments: IMedicament[]) => {
    set({ popularMedicaments });
  },

  GetPopularMedicaments: async () => {
    try {
      if (get().popularMedicaments.length) return;
      set({ loadingPopularMedicaments: true });
      const result = await GetPopularMedicament();

      const medicamentsPromises = result.data.map(async (medicament: any) => {
        const searchResult = await getSearchExternalMedicament(medicament.name);

        const filteredSearchResult = searchResult.data.Respuesta.find(
          (option: any) => option.vnombreproducto === medicament.name
        );

        if (!filteredSearchResult) return;

        return {
          ...filteredSearchResult,
        };
      });

      const allMedicamentsWithSearchResults = await Promise.all(
        medicamentsPromises
      );

      set({
        popularMedicaments: allMedicamentsWithSearchResults.filter(Boolean),
        loadingPopularMedicaments: false,
      });
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message, loadingPopularMedicaments: false });
    }
  },

  DuplicateRecipe: (medicaments: IMedicament[], confirmForm: IRecipes) => {
    set({
      cardMedicament: medicaments.map((medicament) => ({
        ...medicament,
        uicodproducto: medicament.medicament_id,
      })),
      confirmRecipForm: {
        temp: Number(confirmForm.temp),
        weight: Number(confirmForm.weight),
        height: Number(confirmForm.height),
        pressure: confirmForm.pressure,
        saturation: Number(confirmForm.saturation),
        ppm: Number(confirmForm.ppm),
        diagnostic: confirmForm.diagnostic,
        add: confirmForm.add,
        room_id: String(confirmForm.room.id),
      },
    });
  },

  SearchMedicaments: async (search: string = "") => {
    set({ search });
    try {
      const timeIdState = get().timeId;
      timeIdState && clearTimeout(timeIdState);

      const timeId = setTimeout(async () => {
        try {
          set({ loading: true });
          const resultExternal = await getSearchExternalMedicament(search);
          set({
            medicaments: resultExternal.data.Respuesta,
            step: 2,
            loading: false,
            timeId: null,
          });
        } catch (error: any) {
          toast.error(error.message);
          set({ error: error.message, loading: false, timeId: null });
        }
      }, 1000);

      set({ timeId });
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message, loading: false });
    }
  },

  ResetMedicaments: () => {
    set({
      step: 1,
      selectedMedicament: null,
      selectedMedicamentDefault: null,
      cardMedicament: [],
      medicaments: null,
      confirmRecipForm: {
        temp: "",
        weight: "",
        height: "",
        pressure: "",
        saturation: "",
        ppm: "",
        diagnostic: "",
        add: "",
        room_id: "",
      },
    });
  },

  SelectMedicament: async (medicamentId: string) => {
    set({ loadingAction: true });
    try {
      const medicaments = get().medicaments;
      const popularMedicaments = get().popularMedicaments;
      let findMedicament = medicaments?.find(
        (medicament) => medicament.uicodproducto === medicamentId
      );

      if (!findMedicament) {
        findMedicament = popularMedicaments.find(
          (medicament) => medicament.uicodproducto === medicamentId
        );
      }

      set({
        step: 3,
        selectedMedicament: findMedicament,
      });

      //Validate group I
      const medicamentResults = await getMedicamentByCode(
        findMedicament?.uicodproducto ?? ""
      );

      const medicamentResult = medicamentResults.data.products[0];

      const unificatedMedicament = {
        ...findMedicament,
        medicament_id: findMedicament?.uicodproducto,
        family: medicamentResult.saClassification || "",
        group: medicamentResult.saClassification || "",
        name: findMedicament?.vnombreproducto ?? "",
        type: medicamentResult.type || "",
        salt: findMedicament?.vnombresal,
      };

      if (unificatedMedicament.group === "Grupo I") {
        setTimeout(() => {
          set({
            loadingAction: false,
            selectedMedicament: unificatedMedicament,
          });
        }, 200);
        return toast.error("Medicamentos de Fracción 1 no se puede prescribir");
      }

      if (findMedicament) {
        set({
          //step: 3,
          loadingAction: false,
          selectedMedicament: unificatedMedicament,
        });
      }
      setTimeout(() => {
        set({ loadingAction: false });
      }, 200);
    } catch (error: any) {
      set({ error: error.message, loadingAction: false });
    }
  },

  // Accion de update
  CreateMedicament: async (medicamentPayload: IMedicament) => {
    set({ loadingAction: true, error: null });

    try {
      const cardMedicament = get().cardMedicament || [];
      const selectedMedicament = get().selectedMedicament;

      if (selectedMedicament?.group === "Grupo I") {
        setTimeout(() => {
          set({
            loadingAction: false,
          });
        }, 200);
        return toast.error("Medicamentos de Fracción 1 no se puede prescribir");
      }

      set({
        cardMedicament: [...cardMedicament, medicamentPayload],
        step: 1,
        search: "",
      });

      toast.success("Medicamento agregado correctamente");
      return true;
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message });
    } finally {
      set({ loadingAction: false });
    }
  },
}));
