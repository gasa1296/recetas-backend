// authStore.ts
import { registerUser } from "@/services/auth";
import {
    IForm1,
    IForm2,
    IForm3,
    IRegisterPayload,
} from "@/types/Store/Register";
import toast from "react-hot-toast";
import { create } from "zustand";

type IRegisterStore = {
    success: boolean;
    loading: boolean;
    error: string | null;
    form1: IForm1 | null;
    form2: IForm2 | null;
    form3: IForm3 | null;

    setClearForms: () => void;

    setForm1: (form: IForm1) => void;
    setForm2: (form: IForm2) => void;
    setForm3: (form: IForm3) => void;
    setSuccess: (success: boolean) => void;
    handleSubmit: (registerPayload: IRegisterPayload) => Promise<any>;
};

export const useRegisterStore = create<IRegisterStore>((set) => ({
    success: false,
    form1: null,
    form2: null,
    form3: null,
    loading: false,
    error: null,

    setClearForms: () => {
        set({
            form1: null,
            form2: null,
            form3: null,
        });
    },

    setForm1: (form: IForm1) => {
        set({ form1: form });
    },

    setForm2: (form: IForm2) => {
        set({ form2: form });
    },

    setForm3: (form: IForm3) => {
        set({ form3: form });
    },

    setSuccess: (success: boolean) => {
        set({ success });
    },

    handleSubmit: async (registerPayload: IRegisterPayload) => {
        set({ loading: true });
        try {
            const response = await registerUser(registerPayload);
            set({ success: true });
            return response.data;
        } catch (error: any) {
            toast.error(error.message);
            set({ error: error.message });
        } finally {
            set({ loading: false });
        }
    },
}));
