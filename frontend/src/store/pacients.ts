// authStore.ts
import {
    createPatient,
    getPatients,
    getSearchPatients,
    updatePatient,
} from "@/services/pacients";
import { IDefaultPacient, IPacient } from "@/types/Models/Pacient";
import toast from "react-hot-toast";
import { create } from "zustand";

type IState = {
    timeId: any;
    step: number;
    loading: boolean;
    loadingAction: boolean;
    pacients: IPacient[] | null;
    selectedPacient: IPacient | null;
    selectedPacientDefault: IDefaultPacient | null;
    error: string | null;
    GetPacients: () => any;
    SearchPacients: (search: string) => any;
    SetStep: (step: number) => any;
    CreatePacient: (pacientPayload: IPacient) => any;
    UpdatePacient: (pacientPayload: IPacient) => any;
    SelectPacient: (pacientEmail: string) => any;
    ResetPacients: () => any;
};

export const usePacients = create<IState>((set, get) => ({
    // Estado inicial
    timeId: null,
    step: 1,
    pacients: null,
    loading: false,
    loadingAction: false,
    error: null,
    selectedPacient: null,
    selectedPacientDefault: null,

    GetPacients: async () => {
        set({ loading: true });
        try {
            const result = await getPatients();

            set({
                pacients: result.data.data,
            });
        } catch (error: any) {
            toast.error(error.message);
            set({ error: error.message });
        } finally {
            set({ loading: false });
        }
    },

    SetStep: (step: number) => {
        set({ step });
    },

    SearchPacients: async (search: string = "") => {
        set({ loading: true });
        try {
            const timeIdState = get().timeId;
            timeIdState && clearTimeout(timeIdState);

            const timeId = setTimeout(async () => {
                try {
                    const result = await getSearchPatients(search);

                    set({
                        pacients: result.data.data,
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

    ResetPacients: () => {
        set({ selectedPacient: null, selectedPacientDefault: null, step: 1 });
    },

    SelectPacient: (pacientEmail: string) => {
        set({ loadingAction: true });
        try {
            const pacients = get().pacients;
            const findPacient = pacients?.find(
                (pacient) => pacient.email === pacientEmail
            );

            if (findPacient) {
                set({
                    step: 2,
                    selectedPacient: findPacient,
                    selectedPacientDefault: {
                        value: findPacient.email,
                        label: `${findPacient.last_name1} ${findPacient.last_name2}, ${findPacient.first_name} | ${findPacient.email}`,
                    },
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
    CreatePacient: async (pacientPayload: IPacient) => {
        set({ loadingAction: true, error: null });

        try {
            const result = await createPatient(pacientPayload);

            const findPacient = result.data.data;

            set({
                step: 2,
                selectedPacient: findPacient,
                selectedPacientDefault: {
                    value: findPacient.email,
                    label: `${findPacient.last_name1} ${findPacient.last_name2}, ${findPacient.first_name} | ${findPacient.email}`,
                },
            });

            toast.success("Paciente creado correctamente");
            return result;
        } catch (error: any) {
            toast.error(error.message);
            set({ error: error.message });
        } finally {
            set({ loadingAction: false });
        }
    },

    // Accion de update
    UpdatePacient: async (pacientPayload: IPacient) => {
        set({ loadingAction: true, error: null });

        try {
            const result = await updatePatient(pacientPayload);

            toast.success("Paciente actualizado correctamente");
            return result;
        } catch (error: any) {
            toast.error(error.message);
            set({ error: error.message });
        } finally {
            set({ loadingAction: false });
        }
    },
}));
