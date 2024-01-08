export function calculateAge(birthDate: string = "") {
    const currentDate = new Date();
    const birthDateObj = new Date(birthDate);

    let age = currentDate.getFullYear() - birthDateObj.getFullYear();

    // Verificar si aún no se ha cumplido el mes de nacimiento
    if (currentDate.getMonth() < birthDateObj.getMonth()) {
        age--;
    }

    // Verificar si aún no se ha cumplido el día de nacimiento
    if (
        currentDate.getMonth() === birthDateObj.getMonth() &&
        currentDate.getDate() < birthDateObj.getDate()
    ) {
        age--;
    }

    return age;
}
