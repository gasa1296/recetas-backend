import {
    IForgotPayload,
    ILoginPayload,
    IRecoverPayload,
    IRegisterPayload,
} from "@/types/Store/Register";
import { Api } from ".";
import axios from "axios";
const baseUrl = process.env.NEXT_PUBLIC_BASE_URL;

export const registerUser = (registerPayload: IRegisterPayload) => {
    const formData = new FormData();

    Object.keys(registerPayload).forEach((key) => {
        if (
            key !== "specializations" &&
            key !== "rooms" &&
            registerPayload[key] !== null
        ) {
            formData.append(key, registerPayload[key] as string);
        }
    });

    if (
        registerPayload.specializations &&
        Array.isArray(registerPayload.specializations)
    ) {
        registerPayload.specializations.forEach((specialization, index) => {
            Object.keys(specialization).forEach((key) => {
                if (key !== "file") {
                    const value = specialization[key];
                    if (value !== null) {
                        formData.append(
                            `specializations[${index}][${key}]`,
                            value
                        );
                    }
                }
            });

            // Añadir archivo si no es null
            if (specialization.file.length && specialization.file[0] !== null) {
                formData.append(
                    `logo_spec[${index}]`,
                    specialization.file[0],
                    specialization.file[0].name
                );
            }
        });
    }

    // Añadir archivos de habitaciones si existen
    if (registerPayload.rooms && Array.isArray(registerPayload.rooms)) {
        registerPayload.rooms.forEach((room, index) => {
            Object.keys(room).forEach((key) => {
                if (key !== "files") {
                    const value = room[key];
                    if (value !== null) {
                        formData.append(`rooms[${index}][${key}]`, value);
                    }
                }
            });

            // Añadir archivo si no es null
            if (room.files.length && room.files[0] !== null) {
                formData.append(
                    `logo_room[${index}]`,
                    room.files[0],
                    room.files[0].name
                );
            }
        });
    }

    return axios.post(baseUrl + `/api/auth/register`, formData, {
        headers: {
            "Content-Type": "multipart/form-data",
        },
    });
};

export const recoverUser = () => {
    return Api({
        endpoint: `/profile`,
        method: "GET",
    });
};

export const updateProfile = (profilePayload: IRegisterPayload) => {
    return Api({
        endpoint: `/profile`,
        method: "PUT",
        _data: profilePayload,
    });
};

export const loginUser = (loginPayload: ILoginPayload) => {
    return Api({
        endpoint: `/auth/login`,
        method: "POST",
        _data: loginPayload,
    });
};

export const logoutUser = () => {
    return Api({
        endpoint: `/auth/logout`,
        method: "DELETE",
    });
};

export const verifyUser = (id: string, hash: string) => {
    return Api({
        endpoint: `/email/verify/${id}/${hash}`,
        method: "GET",
    });
};

export const forgotPassword = (forgotPayload: IForgotPayload) => {
    return Api({
        endpoint: `/auth/forgot-password`,
        method: "POST",
        _data: forgotPayload,
    });
};

export const recoverPassword = (recoverPayload: IRecoverPayload) => {
    return Api({
        endpoint: `/auth/recover-password`,
        method: "POST",
        _data: recoverPayload,
    });
};
