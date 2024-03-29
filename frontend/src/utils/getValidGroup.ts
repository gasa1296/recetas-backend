import { IMedicament } from "@/types/Models/Medicament";
/*
 export const canAddMedicamentByGroup = async (
  medicamentGroup: string,
  medicaments: IMedicament[]
) => {
  const isGroupIMedication = ["Grupo II", "Grupo III", "Grupo IV"].includes(
    medicamentGroup
  );

  const alreadyExists = medicaments.some((medicament: IMedicament) =>
    ["Grupo II", "Grupo III", "Grupo IV"].includes(medicament.group ?? "")
  );

  return !(isGroupIMedication && alreadyExists);
};
 */
export const canAddMedicamentByGroup = async (
  medicamentGroup: string,
  medicaments: IMedicament[]
) => {
  // Define los grupos especiales.
  const specialGroups = ["Grupo II", "Grupo III"];

  // Determina si el grupo al que se quiere añadir es uno de los grupos especiales.
  const isMedicamentGroupSpecial = specialGroups.includes(medicamentGroup);

  // Busca si hay medicamentos de un grupo especial diferente ya presentes en la lista.
  const hasDifferentSpecialGroup = medicaments.some(
    (medicament) => medicament.group !== medicamentGroup
  );

  if (isMedicamentGroupSpecial) {
    return !hasDifferentSpecialGroup;
  }

  if (
    !isMedicamentGroupSpecial &&
    medicaments.some((medicament) =>
      specialGroups.includes(medicament.group ?? "")
    )
  ) {
    return false;
  }

  // En cualquier otro caso, es válido (p.ej. mismo grupo no especial que ya existe o lista vacía).
  return true;
};
