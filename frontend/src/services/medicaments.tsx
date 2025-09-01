import { IMedicament } from "@/types/Models/Medicament";
import { Api } from ".";
import axios from "axios";

export const getSearchExternalMedicament = (search: string) => {
  return Api({
    endpoint: `prescription/medicaments2`,
    method: "POST",
    _data: { descripcion: search, hash: "initial" },
  });
};

export const getMedicamentByCode = (productCode: string) => {
  return Api({
    endpoint: `prescription/medicaments`,
    method: "POST",
    _data: { products: [{ code: productCode }] },
  });
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

export const GetPopularMedicament = () => {
  return Api({
    endpoint: `/most_used`,
    method: "GET",
  });
};
