import { IRecipes } from "@/types/Models/Recipes";
import { Api } from ".";
import { IMedicament } from "@/types/Models/Medicament";

export const createRecipe = (recipePayload: IRecipes) => {
  return Api({
    endpoint: `/prescription`,
    method: "POST",
    _data: recipePayload,
  });
};

export const getRecipeSign = (recipeId: string) => {
  return Api({
    endpoint: `/prescription/${recipeId}/sign`,
    method: "GET",
  });
};

export const sendRecipeByWhatsapp = (recipeId: string) => {
  return Api({
    method: "GET",
    endpoint: `/room`,
  });
  /*   return Api({
    endpoint: `/prescription/${recipeId}/sign`,
    method: "GET",
  }); */
};

export const sendRecipeByEmail = (recipeId: string) => {
  return Api({
    endpoint: `/prescription/${recipeId}/email`,
    method: "GET",
  });
};
export const addRecipeMedicament = (
  recipeId: string,
  medicamentPayload: IMedicament[]
) => {
  return Api({
    endpoint: `/prescription/${recipeId}/medicament`,
    method: "POST",
    _data: medicamentPayload,
  });
};
