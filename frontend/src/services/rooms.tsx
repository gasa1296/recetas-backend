import { IRoom, ISpecialization } from "@/types/Store/Register";
import { Api } from ".";
import axios from "axios";
const baseUrl = process.env.NEXT_PUBLIC_BASE_URL;

export const getRooms = () => {
  return Api({
    method: "GET",
    endpoint: `/room`,
  });
};

export const getRoomDesigns = () => {
  return Api({
    method: "GET",
    endpoint: `/room/designs`,
  });
};

export const postRooms = (data: IRoom[]) => {
  const formData = new FormData();
  // Añadir archivos de habitaciones si existen
  if (data && Array.isArray(data)) {
    data.forEach((room: any, index) => {
      Object.keys(room).forEach((key) => {
        if (key !== "logo") {
          const value = room[key];
          if (value !== null) {
            formData.append(`data[${index}][${key}]`, value);
          }
        }
      });

      // Añadir archivo si no es null
      if (room.logo.length && room.logo[0] !== null) {
        formData.append(`logo[${index}]`, room.logo[0], room.logo[0].name);
      }
    });
  }

  return axios.post(baseUrl + `/api/room`, formData, {
    headers: {
      "Content-Type": "multipart/form-data",
      Authorization: `Bearer ${localStorage.getItem("sessionToken")}`,
    },
  });
};

export const removeRooms = (room: IRoom) => {
  return Api({
    method: "DELETE",
    endpoint: `/room/${room.id}`,
  });
};

export const updateRooms = (rooms: IRoom[]) => {
  const formData = new FormData();

  if (rooms && Array.isArray(rooms)) {
    rooms.forEach((room: any, index) => {
      if (!room.id) delete room.id;
      Object.keys(room).forEach((key) => {
        if (key !== "logo") {
          const value = room[key];
          if (value !== null) {
            formData.append(`data[${index}][${key}]`, value);
          }
        }
      });

      if (
        room.logo.length &&
        room.logo[0] &&
        typeof room.logo[0] !== "string"
      ) {
        formData.append(`logo[${index}]`, room.logo[0], room.logo[0].name);
      } else {
        formData.append(`data[${index}][logo]`, "");
      }
    });
  }

  return axios.post(baseUrl + `/api/room`, formData, {
    headers: {
      "Content-Type": "multipart/form-data",
      Authorization: `Bearer ${localStorage.getItem("sessionToken")}`,
    },
  });
};
