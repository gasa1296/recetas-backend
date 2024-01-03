const isObject = (obj: object) => obj && typeof obj === "object";

export const validateSameObject = (obj1: any, obj2: any) => {
    // Si ambos son el mismo objeto (incluye 'null' y valores primitivos), son iguales.
    if (obj1 === obj2) return false;

    // Si uno es 'null', o ambos son de diferente tipo (uno es objeto y otro no), son distintos.
    if (!isObject(obj1) || !isObject(obj2)) return true;

    // Comprobación de arrays
    if (Array.isArray(obj1) && Array.isArray(obj2)) {
        if (obj1.length !== obj2.length) return true;
        for (let i = 0; i < obj1.length; i++) {
            if (validateSameObject(obj1[i], obj2[i])) return true;
        }
        return false;
    }

    // Comprobación de objetos
    const keys1 = Object.keys(obj1).sort();
    const keys2 = Object.keys(obj2).sort();

    // Si la cantidad de claves es diferente, los objetos son distintos.
    if (keys1.length !== keys2.length) return true;

    // Comparamos valores clave por clave
    for (let key of keys1) {
        if (!keys2.includes(key) || validateSameObject(obj1[key], obj2[key]))
            return true;
    }

    // No se encontraron diferencias
    return false;
};
