import useCustomEffect from "@/hooks/useCustomEffect";
import { useRoomsStore } from "@/store/rooms";
import React from "react";
import { FaSignature } from "react-icons/fa";
export default function Sign({ nextStep, backStep }: any) {
  const GetRooms = useRoomsStore((state) => state.GetRooms);

  const [error, setError] = React.useState<any>(false);
  const [accepted, setAccepted] = React.useState(false);

  useCustomEffect({ requestGet: GetRooms });

  const handleSubmit = () => {
    if (!accepted) {
      setError("Lo sentimos, debe aceptar el aviso");
      return;
    }

    handleClickFirma();
    /*  nextStep(); */
  };

  // Funciones Callbak de prueba
  function signatureInit(data: any) {
    console.log("Proceso de firma iniciado !!!", data);
  }

  function signatureFinish(data: any) {
    console.log("Proceso de firma finalizado !!!", data);
  }

  const handleClickFirma = () => {
    /* const legalario = new LegalarioSDK({
      organizationId: "6584c7b41621f05565239a53",
      apiKey:
        "adaa85c70a08aa8a5b00bb5233a34b127be5188d2d419fbeb4950a5eba79540a",
      env: "SANDBOX",
    });

    // Inicio del proceso de firma
    legalario.signature({
      signerId: "65abb643c37b226a26017a0d",
      callbacks: {
        onInit: signatureInit,
        onFinish: signatureFinish,
      },
    }); */
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
            Firma tu receta en el recuadro o agrégala desde un archivo como
            imagen
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

        <div className="block md:flex justify-center  py-6">
          <button
            disabled={true}
            className="button-BlacK w-full p-2 font-bold max-w-[660px] disabled:opacity-75"
          >
            Crear receta
          </button>
        </div>
      </section>
    </section>
  );
}
