import { ISpecialization } from "@/types/Store/Register";
import { Api } from ".";
import axios from "axios";
const baseUrl = process.env.NEXT_PUBLIC_BASE_URL;

export const getSpecializations = () => {
  return Api({
    method: "GET",
    endpoint: `/specialization`,
  });
};

export const updateSpecializations = (data: ISpecialization[]) => {
  const formData = new FormData();
  if (data && Array.isArray(data)) {
    data.forEach((specialization: any, index) => {
      if (!specialization.id) delete specialization.id;
      Object.keys(specialization).forEach((key) => {
        if (key !== "logo") {
          const value = specialization[key];
          if (value !== null) {
            formData.append(`data[${index}][${key}]`, value);
          }
        }
      });

      // Añadir archivo si no es null
      if (
        specialization.logo.length &&
        specialization.logo[0] &&
        typeof specialization.logo[0] !== "string"
      ) {
        formData.append(
          `logo[${index}]`,
          specialization.logo[0],
          specialization.logo[0].name
        );
      } else {
        formData.append(`data[${index}][logo]`, "");
      }
    });
  }

  return axios.post(baseUrl + `/api/specialization`, formData, {
    headers: {
      "Content-Type": "multipart/form-data",
      Authorization: `Bearer ${localStorage.getItem("sessionToken")}`,
    },
  });
};

export const removeSpecializations = (data: ISpecialization) => {
  return Api({
    method: "DELETE",
    endpoint: `/specialization/${data.id}`,
  });
};

export const postSpecializations = (data: ISpecialization[]) => {
  const formData = new FormData();
  if (data && Array.isArray(data)) {
    data.forEach((specialization: any, index) => {
      Object.keys(specialization).forEach((key) => {
        if (key !== "logo") {
          const value = specialization[key];
          if (value !== null) {
            formData.append(`data[${index}][${key}]`, value);
          }
        }
      });

      // Añadir archivo si no es null
      if (specialization.logo.length && specialization.logo[0] !== null) {
        formData.append(
          `logo[${index}]`,
          specialization.logo[0],
          specialization.logo[0].name
        );
      }
    });
  }

  return axios.post(baseUrl + `/api/specialization`, formData, {
    headers: {
      "Content-Type": "multipart/form-data",
      Authorization: `Bearer ${localStorage.getItem("sessionToken")}`,
    },
  });
};
