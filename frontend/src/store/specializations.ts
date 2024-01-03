// authStore.ts
import {
    getSpecializations,
    postSpecializations,
    removeSpecializations,
    updateSpecializations,
} from "@/services/specializations";
import { ISpecialization } from "@/types/Store/Register";
import toast from "react-hot-toast";
import { create } from "zustand";

type AuthState = {
    loading: boolean;
    loadingUpdate: boolean;
    specializations: ISpecialization[] | null;
    error: string | null;
    GetSpecializations: () => any;
    UpdateSpecializations: (profilePayload: ISpecialization[]) => any;
};

export const useSpecializationsStore = create<AuthState>((set, get) => ({
    // Estado inicial
    specializations: null,
    loading: false,
    loadingUpdate: false,
    error: null,

    GetSpecializations: async () => {
        set({ loading: true });
        try {
            const result = await getSpecializations();

            set({
                specializations: result.data.data.map(
                    (specialization: any) => ({
                        ...specialization,
                        logo: [specialization.logo],
                    })
                ),
            });
        } catch (error: any) {
            toast.error(error.message);
            set({ error: error.message });
        } finally {
            set({ loading: false });
        }
    },

    // Accion de update
    UpdateSpecializations: async (profilePayload: ISpecialization[]) => {
        set({ loadingUpdate: true, error: null });

        try {
            const specializations = get().specializations;

            //Filter delete
            const deleteSpecializations = specializations?.filter(
                (specialization) => {
                    return !profilePayload.find(
                        (specializationPayload) =>
                            specializationPayload.id === specialization.id
                    );
                }
            );

            const pendingPromise = [];
            pendingPromise.push(updateSpecializations(profilePayload));

            if (deleteSpecializations?.length) {
                deleteSpecializations.map((specialization) =>
                    pendingPromise.push(removeSpecializations(specialization))
                );
            }

            const response = await Promise.all(pendingPromise);

            toast.success("Cuenta actualizada correctamente");
            return response;
        } catch (error: any) {
            toast.error(error.message);
            set({ error: error.message });
        } finally {
            set({ loadingUpdate: false });
        }
    },
}));
