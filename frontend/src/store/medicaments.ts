// authStore.ts
import {
  createMedicament,
  getSearchExternalMedicament,
} from "@/services/medicaments";

import {
  IConfirmRecipForm,
  IMedicament,
  IMedicamentDefault,
  INewMedicament,
} from "@/types/Models/Medicament";
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
  confirmRecipForm: IConfirmRecipForm;
  SearchMedicaments: (search: string) => any;
  ResetMedicaments: () => any;
  SelectMedicament: (medicamentId: string) => any;
  SetStep: (step: number) => any;
  SetConfirmForm: (confirmRecipForm: IConfirmRecipForm) => any;
  SetAllCardMedicament: (medicaments: IMedicament[]) => any;
  CreateMedicament: (medicamentPayload: IMedicament) => any;
};

export const useMedicamentStore = create<IState>((set, get) => ({
  // Estado inicial
  timeId: null,
  medicaments: null,
  step: 1,
  loading: false,
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

  SetStep: (step: number) => {
    set({ step });
  },
  SetConfirmForm: (confirmRecipForm: IConfirmRecipForm) => {
    set({ confirmRecipForm });
  },

  SetAllCardMedicament: (medicaments: IMedicament[]) => {
    set({ cardMedicament: medicaments });
  },

  SearchMedicaments: async (search: string = "") => {
    set({ loading: true });
    try {
      const timeIdState = get().timeId;
      timeIdState && clearTimeout(timeIdState);

      const timeId = setTimeout(async () => {
        try {
          /* const result = await getSearchMedicament(search); */
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
      }, 500);

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

  SelectMedicament: (medicamentId: string) => {
    set({ loadingAction: true });
    try {
      const medicaments = get().medicaments;
      const findMedicament = medicaments?.find(
        (medicament) => medicament.uicodproducto === medicamentId
      );

      if (findMedicament) {
        set({
          step: 3,
          selectedMedicament: findMedicament,
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
      set({
        cardMedicament: [...cardMedicament, medicamentPayload],
        step: 1,
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
