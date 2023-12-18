// authStore.ts

import {
    getRooms,
    postRooms,
    removeRooms,
    updateRooms,
} from "@/services/rooms";
import { IRoom } from "@/types/Store/Register";
import toast from "react-hot-toast";
import { create } from "zustand";

type AuthState = {
    loading: boolean;
    loadingUpdate: boolean;
    rooms: IRoom[] | null;
    error: string | null;
    GetRooms: () => any;
    UpdateRooms: (profilePayload: IRoom[]) => any;
};

export const useRoomsStore = create<AuthState>((set, get) => ({
    // Estado inicial
    rooms: null,
    loading: false,
    loadingUpdate: false,
    error: null,

    GetRooms: async () => {
        set({ loading: true });
        try {
            const result = await getRooms();

            set({
                rooms: result.data.data.map((room: any) => ({
                    ...room,
                    logo: [room.logo],
                    design: room.design.toString(),
                })),
            });
        } catch (error: any) {
            toast.error(error.message);
            set({ error: error.message });
        } finally {
            set({ loading: false });
        }
    },

    // Accion de update
    UpdateRooms: async (profilePayload: IRoom[]) => {
        set({ loadingUpdate: true, error: null });

        try {
            const rooms = get().rooms;

            const deleteRooms = rooms?.filter((room) => {
                return !profilePayload.find(
                    (roomPayload) => roomPayload.id === room.id
                );
            });
            const pendingPromise = [];
            pendingPromise.push(updateRooms(profilePayload));

            if (deleteRooms?.length) {
                deleteRooms.map((room) =>
                    pendingPromise.push(removeRooms(room))
                );
            }

            const response = await Promise.all(pendingPromise);

            toast.success("Cuenta actualizada correctamente");
            return response;
        } catch (error: any) {
            console.log("Asdasd", error);
            toast.error(error.message);
            set({ error: error.message });
        } finally {
            set({ loadingUpdate: false });
        }
    },
}));
