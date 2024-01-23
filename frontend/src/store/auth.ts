// authStore.ts
import {
  forgotPassword,
  loginUser,
  logoutUser,
  recoverPassword,
  recoverUser,
  updateProfile,
  verifyUser,
} from "@/services/auth";
import { IUser } from "@/types/Models/User";
import {
  IForgotPayload,
  ILoginPayload,
  IRecoverPayload,
  IRegisterPayload,
} from "@/types/Store/Register";
import { getRequestError, getRequestErrorArray } from "@/utils/getRequestError";
import { getResetPasswordParams } from "@/utils/getResetPasswordParams";
import toast from "react-hot-toast";
import { create } from "zustand";

type AuthState = {
  isAuth: boolean;
  loading: boolean;
  sessionToken: string | null;
  verifyUser: boolean;
  error: string | null;
  user: IUser | null;
  Login: (loginPayload: ILoginPayload) => any;
  Logout: () => any;
  Verify: (id: string, hash: string) => any;
  ForgotPassword: (forgotPayload: IForgotPayload) => any;
  RecoverPassword: (recoverPayload: IRecoverPayload) => any;
  RecoverUser: (token: string) => any;
  UpdateProfile: (profilePayload: IRegisterPayload) => any;
};

export const useAuthStore = create<AuthState>((set) => ({
  // Estado inicial
  isAuth: false,
  sessionToken: null,
  user: null,
  verifyUser: false,
  loading: false,
  error: null,

  RecoverUser: async (token: string) => {
    try {
      const result = await recoverUser(token);
      await localStorage.setItem("sessionUser", JSON.stringify(result.data));
      await localStorage.setItem("sessionToken", token);
      set({
        isAuth: true,
        sessionToken: token,
        user: result.data,
      });
    } catch (error: any) {
      await localStorage.removeItem("sessionToken");
      toast.error(error.message);
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  // Acción de login
  Login: async (loginPayload: ILoginPayload) => {
    set({ loading: true, error: null });
    try {
      const response = await loginUser(loginPayload);
      set({
        isAuth: true,
        sessionToken: response.data.token,
        user: response.data.user,
      });
      await localStorage.setItem("sessionToken", response.data.token);
      await localStorage.setItem(
        "sessionUser",
        JSON.stringify(response.data.user)
      );
      return response.data;
    } catch (error: any) {
      const message = getRequestErrorArray(error);
      toast.error(message);
      set({ error: message });
    } finally {
      set({ loading: false });
    }
  },

  // Accion de update
  UpdateProfile: async (profilePayload: IRegisterPayload) => {
    set({ loading: true, error: null });
    try {
      const response = await updateProfile(profilePayload);
      set({
        user: response.data,
      });
      toast.success("Cuenta actualizada correctamente");
      return response.data;
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  // Acción de logout
  Logout: async () => {
    set({ loading: true, error: null });
    try {
      const response = await logoutUser();
      set({ isAuth: false, user: null });
      await localStorage.removeItem("sessionToken");
      await localStorage.removeItem("sessionUser");
      return response.data;
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  // Acción de verify
  Verify: async (id: string, hash: string) => {
    set({ loading: true, error: null });
    try {
      const response = await verifyUser(id, hash);
      set({ verifyUser: true, user: response.data });
      toast.success("Cuenta verificada correctamente");
      return response.data;
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  // Acción de forgot password
  ForgotPassword: async (forgotPayload: IForgotPayload) => {
    set({ loading: true, error: null });
    try {
      const response = await forgotPassword(forgotPayload);
      set({ isAuth: false, user: null });
      return response.data;
    } catch (error: any) {
      toast.error(error.message);
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },

  // Acción de recover password
  RecoverPassword: async (recoverPayload: IRecoverPayload) => {
    set({ loading: true, error: null });
    try {
      const { token, email } = getResetPasswordParams();
      const response = await recoverPassword(
        { ...recoverPayload, email, token },
        token || ""
      );
      set({ isAuth: false, user: null });
      toast.success("Contraseña actualizada correctamente");
      return response;
    } catch (error: any) {
      console.log(error);
      toast.error(error.response.data.message || error.message);
      set({ error: error.message });
    } finally {
      set({ loading: false });
    }
  },
}));
