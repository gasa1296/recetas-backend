// authStore.ts
import { autopopulateProfile, registerUser } from "@/services/auth";
import {
  IForm1,
  IForm2,
  IForm3,
  IRegisterPayload,
} from "@/types/Store/Register";
import { getRequestError } from "@/utils/getRequestError";
import toast from "react-hot-toast";
import { create } from "zustand";

type IRegisterStore = {
  success: boolean;
  loading: boolean;
  error: string | null;
  form1: IForm1 | null;
  form2: IForm2 | null;
  form3: IForm3 | null;
  enableSearch: boolean;

  setClearForms: () => void;

  setForm1: (form: IForm1) => void;
  setForm2: (form: IForm2) => void;
  setForm3: (form: IForm3) => void;
  setSuccess: (success: boolean) => void;
  handleSubmit: (registerPayload: IRegisterPayload) => Promise<any>;
  handleAutoPopulate: (search: string) => Promise<any>;
};

export const useRegisterStore = create<IRegisterStore>((set) => ({
  success: false,
  form1: null,
  form2: null,
  form3: null,
  enableSearch: false,
  loading: false,
  error: null,

  setClearForms: () => {
    set({
      form1: null,
      form2: null,
      form3: null,
      enableSearch: false,
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
      const result = await autopopulateProfile(registerPayload.email || "");

      if (result && result.data.contacts) {
        const contact = result.data.contacts[0] || null;
        registerPayload.idCX = contact.datosGenerales.id;
        registerPayload.clienteEcommerce =
          contact.datosGenerales.clienteEcommerce === "No" ? false : true;
      }

      const response = await registerUser(registerPayload);
      set({ success: true });
      return response.data;
    } catch (error: any) {
      const message = getRequestError(error);
      toast.error(message);
      set({ error: message });
    } finally {
      set({ loading: false });
    }
  },

  handleAutoPopulate: async (search: string) => {
    set({ loading: true });
    try {
      const result = await autopopulateProfile(search);

      if (!result.data.results) {
        toast.error("Medico no encontrado!");
        return set({ loading: false, form1: null, enableSearch: false });
      }
      setTimeout(() => {
        const contact = result.data.contacts[0] || null;

        const user = {
          first_name: contact.datosGenerales.nombre,
          last_name1: contact.datosGenerales.apellidoPaterno,
          last_name2: contact.datosGenerales.apellidoMaterno,
          email: contact.listaCorreoElectronico[0].correroElectronico,
          gender: contact.datosGenerales.sexo === "Masculino" ? "0" : "1",
          phone1:
            contact.listaTelefonos &&
            contact.listaTelefonos[0].telefono.NumeroTelefonico,
          phone2: "",
          fesa: "",
          password: "",
          confirmPassword: "",
        };
        toast.success("Medico encontrado satisfactoriamente!");
        set({ loading: false, form1: user, enableSearch: true });
      }, 1000);
    } catch (error: any) {
      const message = getRequestError(error);
      toast.error(message);
      set({ error: message, loading: false, enableSearch: false, form1: null });
    }
  },
}));
