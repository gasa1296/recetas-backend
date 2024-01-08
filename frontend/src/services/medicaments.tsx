import { IMedicament } from "@/types/Models/Medicament";
import { Api } from ".";
import axios from "axios";

export const getSearchExternalMedicament = (search: string) => {
    return axios.post(
        "https://w9gkg4xp3k.execute-api.us-east-1.amazonaws.com/Prod/api/preproductos",
        { descripcion: search, hash: "initial" }
    );
};

export const createMedicament = (pacientPayload: IMedicament) => {
    return Api({
        endpoint: `/medicament`,
        method: "POST",
        _data: pacientPayload,
    });
};

export const updateMedicament = (pacientPayload: IMedicament) => {
    return Api({
        endpoint: `/medicament`,
        method: "PUT",
        _data: pacientPayload,
    });
};
