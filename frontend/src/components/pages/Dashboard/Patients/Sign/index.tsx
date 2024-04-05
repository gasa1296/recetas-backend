import useCustomEffect from "@/hooks/useCustomEffect";
import { useRecipeStore } from "@/store/recipes";
import { useRoomsStore } from "@/store/rooms";
import React from "react";
import { FaSignature } from "react-icons/fa";
export default function Sign({ nextStep, backStep }: any) {
  const GetRooms = useRoomsStore((state) => state.GetRooms);
  const recipes = useRecipeStore((state) => state.recipes);

  const signRecipes = recipes.find((recipe) => recipe.sign);

  const [error, setError] = React.useState<any>(false);
  const [accepted, setAccepted] = React.useState(false);

  useCustomEffect({ requestGet: GetRooms });

  const handleSubmit = () => {
    if (!accepted) {
      setError("Lo sentimos, debe aceptar el aviso");
      return;
    }

    handleClickFirma();
  };

  function signatureFinish(data: any) {
    if (
      data.documents[0].status === "approved" &&
      data.signer.status === "confirmed"
    )
      nextStep();
  }
  const handleClickFirma = async () => {
    const legalario = new (window as any).LegalarioSDK({
      organizationId: process.env.NEXT_PUBLIC_LEGALARIO_ORGANIZATION_ID,
      apiKey: process.env.NEXT_PUBLIC_LEGALARIO_KEY,
      env: process.env.NEXT_PUBLIC_LEGALARIO_ENVIRONMENT,
    });

    await legalario.signature(
      {
        signerId: signRecipes.sign,
        modules: ["documents", "signature"],
        authType: "NONE",
        callbacks: {
          onFinish: signatureFinish,
        },
      },
      1000
    );
  };

  return (
    <section className=" mb-8  ">
      <div className="flex items-center  border-Tab p-2 ps-3 mt-4">
        <FaSignature color="#Fff " size={28} />
        <p className="text-[#fff] text-[26px] ms-3">Firmar receta</p>
      </div>

      <section className="container-Patiens px-8 py-5">
        <div className="">
          <h6 className="text-[20px] text-[#1A1A1A] font-bold my-4 text-center mt-10">
            Firma tu receta en el recuadro
          </h6>
          {/*   <div className="container-sing mx-auto"></div> */}

          <div className="flex justify-center">
            <button
              onClick={handleSubmit}
              className="button-white w-full max-w-[300px] mx-auto p-2 mt-3"
            >
              Firmar documento
            </button>
          </div>
        </div>

        <div className="text-center mt-6">
          <div className="flex justify-center">
            <input
              onChange={() => {
                setAccepted(!accepted);
                setError(false);
              }}
              checked={accepted}
              type="checkbox"
              id="privacy"
              name="privacy"
            />
            <label htmlFor="privacy" className="text-[16px] pl-3">
              Acepto el <span> Aviso de privacidad </span>{" "}
            </label>
          </div>

          {error && <p className="text-[12px] text-[#CB2E25] mt-2">{error}</p>}
        </div>
      </section>
    </section>
  );
}
