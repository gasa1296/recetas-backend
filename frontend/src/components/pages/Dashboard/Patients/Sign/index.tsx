import useCustomEffect from "@/hooks/useCustomEffect";
import { useRecipeStore } from "@/store/recipes";
import { useRoomsStore } from "@/store/rooms";
import React from "react";
import { FaSignature } from "react-icons/fa";
export default function Sign({ nextStep, backStep }: any) {
  const GetRooms = useRoomsStore((state) => state.GetRooms);
  const recipes = useRecipeStore((state) => state.recipes);
  const handleUploadDocument = useRecipeStore(
    (state) => state.handleUploadDocument
  );

  const signRecipes = recipes.find((recipe) => recipe.sign);

  const [currentSignIndex, setCurrentSignIndex] = React.useState(0);
  const allSignatures = signRecipes.sign;

  const [error, setError] = React.useState<any>(false);
  const [accepted, setAccepted] = React.useState(false);

  useCustomEffect({ requestGet: GetRooms });

  async function signatureFinish(data: any, signerIndex: number) {
    if (
      data.documents[0].status === "approved" &&
      data.signer.status === "confirmed"
    ) {
      if (signerIndex < allSignatures.length - 1) {
        setCurrentSignIndex(signerIndex + 1);
        performSignature(signerIndex + 1);
      } else {
        nextStep();
      }
      console.log("DATA", data);
      handleUploadDocument(
        signRecipes?.id,
        data.documents[0].id,
        data.documents[0].base64_sign || data.documents[0].base64
      );
    } else {
      setError("Una de las firmas no se completó exitosamente.");
    }
  }

  function performSignature(signerIndex: number) {
    const signerId = allSignatures[signerIndex].data.signers[0].id;
    const legalario = new (window as any).LegalarioSDK({
      organizationId:
        allSignatures[signerIndex].data.user_document.organization_id ||
        process.env.NEXT_PUBLIC_LEGALARIO_ORGANIZATION_ID,
      apiKey: process.env.NEXT_PUBLIC_LEGALARIO_KEY,
      env: process.env.NEXT_PUBLIC_LEGALARIO_ENVIRONMENT,
    });

    legalario
      .signature(
        {
          signerId: signerId,
          modules: ["documents", "signature"],
          authType: "NONE",
          callbacks: {
            onFinish: (data: any) => signatureFinish(data, signerIndex),
          },
        },
        1000
      )
      .catch((error: any) => {
        setError("Ha ocurrido un error durante el proceso de firma.");
      });
  }

  const startSigningProcess = async () => {
    if (!accepted) {
      setError("Lo sentimos, debe aceptar el aviso");
      return;
    }

    setError(null);

    performSignature(0);
  };

  return (
    <section className=" mb-8  ">
      <div className="flex items-center  border-Tab p-2 ps-3 mt-4">
        <FaSignature color="#Fff " size={28} />
        <p className="text-[#fff] text-[26px] ms-3">Firmar receta</p>
      </div>

      <section className="container-Patiens px-8 py-5">
        <div className="mt-6">
          <h6 className="text-[20px] text-[#1A1A1A] font-bold my-4 text-center mt-10">
            Favor de aceptar aviso de privacidad y posterior dar clic en Firmar
            receta ({currentSignIndex} / {allSignatures.length} Firmados)
          </h6>

          <div className="text-center ">
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

            {error && (
              <p className="text-[12px] text-[#CB2E25] mt-2">{error}</p>
            )}
          </div>
          <div className="flex justify-center">
            <button
              onClick={startSigningProcess}
              className="button-white w-full max-w-[300px] mx-auto p-2 mt-3"
            >
              Firmar receta
            </button>
          </div>
        </div>
      </section>
    </section>
  );
}
