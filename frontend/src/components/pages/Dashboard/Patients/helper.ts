export const colourStyles: any = {
    control: (styles: any) => ({
        ...styles,
        backgroundColor: "white",
        minHeight: 50,
    }),

    option: (styles: any, { isSelected }: any) => {
        return {
            ...styles,
            backgroundColor: isSelected ? "#E7F1F7" : "white",
            color: "black",
        };
    },
};
