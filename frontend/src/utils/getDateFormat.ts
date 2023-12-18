export const getDateFormat = (date: string) => {
    let now;

    if (date) {
        now = new Date(date);
    } else {
        now = new Date();
    }

    // Formatear la hora en un formato de 24 horas (HH:MM).
    const formattedTime = now.toLocaleTimeString("es-ES", {
        hour: "2-digit",
        minute: "2-digit",
    });

    // Formatear la fecha para mostrar el día de la semana, el día del mes y el mes.
    const formattedDate = now.toLocaleDateString("es-ES", {
        weekday: "short", // nombre corto del día de la semana
        day: "numeric", // día del mes
        month: "short", // nombre corto del mes
    });

    // Corregir el formato de la fecha para cumplir con el requerimiento 'Jue 16 de nov'.
    const dateParts = formattedDate.split(" ");
    const correctedDate = `${dateParts[0]} ${dateParts[1]} de ${dateParts[2]}`;

    return { formattedTime, correctedDate };
};
