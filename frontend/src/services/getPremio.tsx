import {Api} from ".";

export const getPremioStore = (pagination: number) => {
  return Api({
    endpoint: `/prizes/?page=${pagination}&isFree=true`,
    method: "GET",
  });
};
