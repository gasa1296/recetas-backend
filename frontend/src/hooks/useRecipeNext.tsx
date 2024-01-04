import { useMedicamentStore } from "@/store/medicaments";
import React, { useEffect } from "react";

interface Props {
    nextStep: (value: number) => void;
}

export default function useRecipeNext({ nextStep }: Props) {
    const { enableConfirmation, SetEnalbleConfirmation } = useMedicamentStore(
        (state) => ({
            enableConfirmation: state.enableConfirmation,
            SetEnalbleConfirmation: state.SetEnalbleConfirmation,
        })
    );

    useEffect(() => {
        if (enableConfirmation) {
            SetEnalbleConfirmation(false);
            nextStep(2);
        }
    }, [enableConfirmation]);
    return {};
}
