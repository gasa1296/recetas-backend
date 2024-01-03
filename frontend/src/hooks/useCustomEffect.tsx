import React, { useEffect } from "react";

export default function useCustomEffect({ requestGet }: { requestGet: any }) {
    useEffect(() => {
        const timeId = setTimeout(() => {
            requestGet && requestGet();
        }, 200);
        return () => clearTimeout(timeId);
    }, []);

    return {};
}
