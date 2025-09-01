// authStore.ts
import {
  autopopulateProfile,
  autopopulateProfileByName,
  registerUser,
} from "@/services/auth";
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
  errorMessage: boolean;
  error: string | null;
  form1: IForm1 | null;
  form2: IForm2 | null;
  form3: IForm3 | null;
  enableSearch: boolean;
  idCX: string | null;
  setClearForms: () => void;

  setForm1: (form: IForm1) => void;
  setForm2: (form: IForm2) => void;
  setForm3: (form: IForm3) => void;
  setSuccess: (success: boolean) => void;
  handleSubmit: (registerPayload: IRegisterPayload) => Promise<any>;
  handleAutoPopulate: (
    search: string,
    password?: string,
    type?: string
  ) => Promise<any>;
  handleAutoPopulateByName: (payload: {
    nombre: string;
    apellidoPat: string;
    apellidoMat: string;
  }) => Promise<any>;
  setSelectedOption: (option: any) => void;
};

export const useRegisterStore = create<IRegisterStore>((set) => ({
  success: false,
  form1: null,
  form2: null,
  form3: null,
  idCX: null,
  errorMessage: false,
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

  setSelectedOption: (contact: any) => {
    set({ loading: true, errorMessage: false });
    try {
      setTimeout(() => {
        const user = {
          first_name: contact.datosGenerales.nombre,
          last_name1: contact.datosGenerales.apellidoPaterno,
          last_name2: contact.datosGenerales.apellidoMaterno,
          email:
            contact.selectedEmail ||
            contact.listaCorreoElectronico[0].correroElectronico,
          gender: contact.datosGenerales.sexo === "Masculino" ? "M" : "F",
          phone1:
            contact.listaTelefonos &&
            contact.listaTelefonos.map(
              (tlf: any) => tlf.telefono.NumeroTelefonico
            ),
          phone2: "",
          fesa: "",
          password: "",
          confirmPassword: "",
        };

        const cedulas =
          contact.listaCedula?.map((cedula: any) => ({
            name: cedula.especialidad || "",
            identification: cedula.cedulaProfesional || "",
            id_ext: cedula.ID || "",
          })) || null;

        const direcciones =
          contact.listaDireccion?.map((direccion: any) => ({
            name: "",
            zip: direccion.direccion.codigoPostal || "",
            street: direccion.direccion.calle || "",
            colony: direccion.direccion.colonia || "",
            state: direccion.direccion.estado || "",
            delegation: direccion.direccion.delgacionMunicipio || "",
            n_exterior: direccion.direccion.numeroExterior || "",
            n_interior: direccion.direccion.numeroInterior || "",
            address:
              direccion.direccion.calle +
              " " +
              direccion.direccion.numeroExterior,
            phone: "",
            id_ext: direccion.direccion.id_externo || "",
          })) || null;

        toast.success(`Médico seleccionado!`);

        console.log(user);
        console.log(cedulas);
        console.log(direcciones);
        set({
          loading: false,
          idCX: contact.datosGenerales.idExterno,
          form1: user,
          form2: { specializations: cedulas },
          form3: { rooms: direcciones },
          enableSearch:
            contact.datosGenerales.clienteEcommerce === "No" &&
            contact.datosGenerales.medicoCalificado === "No"
              ? false
              : true,
        });
      }, 300);
    } catch (error) {
      toast.error("Error al seleccionar el médico");
      set({ loading: false });
    }
  },

  handleSubmit: async (registerPayload: IRegisterPayload) => {
    set({ loading: true, errorMessage: false });
    try {
      const payload = { ...registerPayload };
      const result = await autopopulateProfile(payload.email || "");

      if (result && result.data.contacts) {
        const contact = result.data.contacts[0] || null;
        payload.idCX = contact.datosGenerales.idExterno;
        payload.clienteEcommerce =
          contact.datosGenerales.clienteEcommerce === "No" ? 0 : 1;
      }

      payload.phone1 = JSON.stringify(
        payload.phone1?.map((phone: string) => ({ phone })) || []
      );

      const response = await registerUser(payload);
      set({ success: true });
      return response.data;
    } catch (error: any) {
      const includeErrorEmail =
        error?.response?.data?.message?.includes("RFC 2606");
      if (includeErrorEmail) {
        set({ success: true, errorMessage: true });
        return true;
      } else {
        const message = getRequestError(error);
        toast.error(message);
        set({ error: message });
      }
    } finally {
      set({ loading: false });
    }
  },

  handleAutoPopulate: async (
    search: string,
    password?: string,
    type?: string
  ) => {
    set({ loading: true });
    try {
      const result = await autopopulateProfile(search);

      if (!result.data.results) {
        toast.error(`Médico no encontrado ${type ? `por ${type}` : ""}!`);
        set({
          loading: false,
          idCX: null,
          form1:
            type === "email"
              ? {
                  email: search,
                  password: "",
                  confirmPassword: "",
                  phone1: "",
                  phone2: "",
                  gender: "",
                  fesa: "",
                  first_name: "",
                  last_name1: "",
                  last_name2: "",
                }
              : null,
          enableSearch: false,
        });

        return false;
      }
      setTimeout(() => {
        const contact = result.data.contacts[0] || null;

        const user = {
          first_name: contact.datosGenerales.nombre,
          last_name1: contact.datosGenerales.apellidoPaterno,
          last_name2: contact.datosGenerales.apellidoMaterno,
          email: contact.listaCorreoElectronico[0].correroElectronico,
          gender: contact.datosGenerales.sexo === "Masculino" ? "M" : "F",
          phone1:
            contact.listaTelefonos &&
            contact.listaTelefonos.map(
              (tlf: any) => tlf.telefono.NumeroTelefonico
            ),
          phone2: "",
          fesa: "0",
          password: password || "",
          confirmPassword: password || "",
        };

        const cedulas =
          contact.listaCedula?.map((cedula: any) => ({
            name: cedula.especialidad || "",
            identification: cedula.cedulaProfesional || "",
            id_ext: cedula.ID || "",
          })) || null;

        const direcciones =
          contact.listaDireccion?.map((direccion: any) => ({
            name: "",
            zip: direccion.direccion.codigoPostal || "",
            street: direccion.direccion.calle || "",
            colony: direccion.direccion.colonia || "",
            state: direccion.direccion.estado || "",
            delegation: direccion.direccion.delgacionMunicipio || "",
            n_exterior: direccion.direccion.numeroExterior || "",
            n_interior: direccion.direccion.numeroInterior || "",
            address:
              direccion.direccion.calle +
              " " +
              direccion.direccion.numeroExterior,
            phone: "",
            id_ext: direccion.direccion.id_externo || "",
          })) || null;

        toast.success(
          `Médico encontrado satisfactoriamente ${type ? `por ${type}` : ""}!`
        );

        set({
          loading: false,
          idCX: contact.datosGenerales.idExterno,
          form1: user,
          form2: { specializations: cedulas },
          form3: { rooms: direcciones },
          enableSearch:
            contact.datosGenerales.medicoVisitable !== "No" ? false : true,
        });
      }, 1000);

      return true;
    } catch (error: any) {
      const message = getRequestError(error);
      toast.error(message);
      set({ error: message, loading: false, enableSearch: false, form1: null });
    }
  },

  handleAutoPopulateByName: async (payload: {
    nombre: string;
    apellidoPat: string;
    apellidoMat: string;
  }) => {
    set({ loading: true });
    try {
      const result = await autopopulateProfileByName(payload);

      if (!result.data.results || result?.data?.contacts?.length >= 1) {
        if (result?.data?.contacts?.length >= 1) {
          toast.success(
            "Médicos encontrado, por favor seleccione el médico correcto"
          );
        } else {
          toast.error(`Médico no encontrado!`);
        }

        set({
          loading: false,
          idCX: null,
          form1: {
            email: "",
            password: "",
            confirmPassword: "",
            phone1: "",
            phone2: "",
            gender: "",
            fesa: "",
            first_name: payload.nombre,
            last_name1: payload.apellidoPat,
            last_name2: payload.apellidoMat,
          },

          enableSearch: false,
        });

        return result?.data?.contacts || [];
      }
      setTimeout(() => {
        const contact = result.data.contacts[0] || null;

        const user = {
          first_name: contact.datosGenerales.nombre,
          last_name1: contact.datosGenerales.apellidoPaterno,
          last_name2: contact.datosGenerales.apellidoMaterno,
          email: contact.listaCorreoElectronico[0].correroElectronico,
          gender: contact.datosGenerales.sexo === "Masculino" ? "M" : "F",
          phone1:
            contact.listaTelefonos &&
            contact.listaTelefonos.map(
              (tlf: any) => tlf.telefono.NumeroTelefonico
            ),
          phone2: "",
          fesa: "",
          password: "",
          confirmPassword: "",
        };

        const cedulas =
          contact.listaCedula?.map((cedula: any) => ({
            name: cedula.especialidad || "",
            identification: cedula.cedulaProfesional || "",
            id_ext: cedula.ID || "",
          })) || null;

        const direcciones =
          contact.listaDireccion?.map((direccion: any) => ({
            name: "",
            zip: direccion.direccion.codigoPostal || "",
            street: direccion.direccion.calle || "",
            colony: direccion.direccion.colonia || "",
            state: direccion.direccion.estado || "",
            delegation: direccion.direccion.delgacionMunicipio || "",
            n_exterior: direccion.direccion.numeroExterior || "",
            n_interior: direccion.direccion.numeroInterior || "",
            address:
              direccion.direccion.calle +
              " " +
              direccion.direccion.numeroExterior,
            phone: "",
            id_ext: direccion.direccion.id_externo || "",
          })) || null;

        toast.success(`Médico encontrado satisfactoriamente !`);
        set({
          loading: false,
          idCX: contact.datosGenerales.idExterno,
          form1: user,
          form2: { specializations: cedulas },
          form3: { rooms: direcciones },
          enableSearch:
            contact.datosGenerales.medicoVisitable !== "No" ? false : true,
        });
      }, 1000);

      return result.data.contacts || [];
    } catch (error: any) {
      const message = getRequestError(error);
      toast.error(message);
      set({ error: message, loading: false, enableSearch: false, form1: null });
    }
  },
}));
