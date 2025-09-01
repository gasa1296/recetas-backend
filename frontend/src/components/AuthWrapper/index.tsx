import { useRouter } from "next/router";
import React, { useEffect, useState } from "react";
import { validateAuthPath } from "./helper";
import { useAuthStore } from "@/store/auth";
import AuthLayout from "../Layouts/AuthLayout";
import Loading from "../Loading";
import { useRoomsStore } from "@/store/rooms";
import useCustomEffect from "@/hooks/useCustomEffect";
import AuthDesignLayout from "../Layouts/AuthDesignLayout";

interface Props {
  children: JSX.Element;
}
export default function AuthWrapper({ children }: Props) {
  const [active, setActive] = useState(false);
  const [loading, setLoading] = useState(true);
  const isAuth = useAuthStore((state) => state.isAuth);
  const RecoverUser = useAuthStore((state) => state.RecoverUser);

  const getRoomDesigns = useRoomsStore((state) => state.getRoomDesigns);

  const router = useRouter();

  const getAuthSession = async () => {
    const searchParams = new URLSearchParams(window.location.search);
    const externalToken = searchParams.get("externalToken");

    let token;
    if (externalToken) {
      token = externalToken as string;
    } else {
      token = await localStorage.getItem("sessionToken");
    }

    if (!token) {
      setActive(true);
      setLoading(false);
      return;
    }

    const result = await RecoverUser(token || "", externalToken ? true : false);
    if (result?.recetasUser === false) router.push("/custom-register");
    else router.push("/dashboard");

    setTimeout(() => setLoading(false), 200);
    setActive(true);
  };

  useCustomEffect({ requestGet: getRoomDesigns });

  useEffect(() => {
    getAuthSession();
  }, []);

  useEffect(() => {
    if (active) {
      if (validateAuthPath(router.pathname, isAuth)) router.push("/");
    }

    //eslint-disable-next-line
  }, [router.pathname, isAuth, active]);

  if (loading || !active)
    return (
      <AuthDesignLayout>
        <Loading />
      </AuthDesignLayout>
    );

  return <>{children}</>;
}
