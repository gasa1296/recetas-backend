import useCustomEffect from "@/hooks/useCustomEffect";
import { useRoomsStore } from "@/store/rooms";
import React from "react";
import { FaSignature } from "react-icons/fa";
export default function Sign({ nextStep, backStep }: any) {
    const GetRooms = useRoomsStore((state) => state.GetRooms);
    const rooms = useRoomsStore((state) => state.rooms);
    const [selectedRoom, setSelectedRoom] = React.useState<any>(null);
    const [error, setError] = React.useState<any>(false);
    const [accepted, setAccepted] = React.useState(false);

    useCustomEffect({ requestGet: GetRooms });

    const handleSubmit = () => {
        if (!accepted) {
            setError("Lo sentimos, debe aceptar el aviso");
            return;
        }

        if (!selectedRoom) {
            setError("Lo sentimos, debe seleccionar un consultorio");
            return;
        }

        nextStep();
    };

    // Funciones Callbak de prueba
    /*     function signatureInit(data) {
        console.log("Proceso de firma iniciado !!!", data);
    }

    function signatureFinish(data) {
        console.log("Proceso de firma finalizado !!!", data);
    } */

    /*     const handleClickFirma = () => {
        const legalario = new LegalarioSDK({
            organizationId: organizationId,
            apiKey: apiKey,
            env: "SANDBOX",
        });

        // Inicio del proceso de firma
        legalario.signature({
            signerId: signerId,
            callbacks: {
                onInit: signatureInit,
                onFinish: signatureFinish,
            },
        });
    };
 */
    return (
        <section className=" mb-8  ">
            <div className="flex items-center  border-Tab p-2 ps-3 mt-4">
                <FaSignature color="#Fff " size={28} />
                <p className="text-[#fff] text-[26px] ms-3">Confirmar receta</p>
            </div>
            <section className="container-Patiens px-8 py-5">
                <h6 className="text-[20px] text-[#1A1A1A] font-bold my-4 ">
                    Selecciona el consultorio para generar tu receta electrónica
                </h6>
                <section className="flex flex-wrap justify-start mt-10">
                    {rooms?.map((room: any, index: number) => (
                        <div className="flex items-start card-sing w-[100%] md:w-[40%] md:min-w-[400px] p-2 md:mr-5 mt-5">
                            <input
                                type="radio"
                                className="form-check-input mt-1"
                                value={room.id}
                                name="room"
                                onChange={(e) =>
                                    setSelectedRoom(e.target.value)
                                }
                                id={`room-${index + 1}`}
                            />
                            <label
                                className="ms-4"
                                htmlFor={`room-${index + 1}`}
                            >
                                <h5 className="title-card">{room.name}</h5>
                                <p className="text-card">{room.address}</p>
                                <p className="text-card">{room.street}</p>
                                <p className="text-card">{room.state}</p>
                            </label>
                        </div>
                    ))}
                </section>
                <div className="">
                    <h6 className="text-[20px] text-[#1A1A1A] font-bold my-4 text-center mt-10">
                        Firma tu receta en el recuadro o agrégala desde un
                        archivo como imagen
                    </h6>
                    {/*   <div className="container-sing mx-auto"></div> */}

                    <div className="flex justify-center">
                        <button
                            onClick={() => {
                                backStep();
                            }}
                            className="button-white w-full max-w-[300px] mx-auto p-2 mt-3"
                        >
                            Agregar firma como imagen
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

                    {error && (
                        <p className="text-[12px] text-[#CB2E25] mt-2">
                            {error}
                        </p>
                    )}
                </div>

                <div className="block md:flex justify-center  py-6">
                    <button
                        onClick={handleSubmit}
                        className="button-BlacK w-full p-2 font-bold"
                    >
                        Crear receta
                    </button>
                </div>
            </section>
        </section>
    );
}
