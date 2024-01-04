import { Api } from ".";
import { IPacient } from "@/types/Models/Pacient";

export const getSearchPatients = (search: string) => {
    return Api({
        method: "GET",
        endpoint: `/patient?search=${search}`,
    });
};
export const getPatients = () => {
    return Api({
        method: "GET",
        endpoint: `/patient`,
    });
};

export const createPatient = (pacientPayload: IPacient) => {
    return Api({
        endpoint: `/patient`,
        method: "POST",
        _data: pacientPayload,
    });
};

export const updatePatient = (pacientPayload: IPacient) => {
    return Api({
        endpoint: `/patient`,
        method: "PUT",
        _data: pacientPayload,
    });
};
