import FormGenerator from "@/components/FormGenerator";
import { Field } from "@/types/Generals/FormGenerator";
import { IRoom } from "@/types/Store/Register";
import { FaSave } from "react-icons/fa";
import Receta1 from "@/assets/images/recetas/Receta1.png";
import Receta2 from "@/assets/images/recetas/Receta2.png";
import Receta3 from "@/assets/images/recetas/Receta3.png";
import React from "react";
import { useRoomsStore } from "@/store/rooms";
import useCustomEffect from "@/hooks/useCustomEffect";
import Loading from "@/components/Loading";

export default function Offices() {
    const { rooms, GetRooms, loading, loadingUpdate, UpdateRooms } =
        useRoomsStore((state) => ({
            loading: state.loading,
            loadingUpdate: state.loadingUpdate,
            rooms: state.rooms,
            GetRooms: state.GetRooms,
            UpdateRooms: state.UpdateRooms,
        }));

    const submitData = async (data: { rooms: IRoom[] }) => {
        const result = await UpdateRooms(data.rooms);
        if (result) GetRooms();
    };

    useCustomEffect({ requestGet: GetRooms });

    const fields: Field[] = [
        {
            label: "Dirección del consultorio principal",
            name: "title",
            required: true,
            type: "title",
            width: 100,
        },

        {
            label: "",
            name: "rooms",
            type: "subForm",
            buttonAddText: "Agregar otro consultorio",
            maxFile: 1,
            width: 100,
            default: rooms || [
                {
                    name: "",
                    zip: "",
                    street: "",
                    colony: "",
                    state: "",
                    delegation: "",
                    n_exterior: "",
                    n_interior: "",
                    address: "",
                    phone: "",
                    file: null,
                    design: null,
                },
            ],
            form: [
                {
                    label: "Dirección del consultorio",
                    name: "title",
                    required: true,
                    type: "title",
                    width: 100,
                },
                {
                    name: "id",
                    required: true,
                    type: "invisible",
                    subFormKey: "id",
                    default: rooms || "",
                },
                {
                    label: "Nombre del consultorio *",
                    name: "name",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "name",
                    default: rooms || "",
                },
                {
                    label: "Codigo Postal *",
                    name: "zip",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "zip",
                    default: rooms || "",
                },
                {
                    label: "Calle *",
                    name: "street",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "street",
                    default: rooms || "",
                },
                {
                    label: "Colonia *",
                    name: "colony",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "colony",
                    default: rooms || "",
                },
                {
                    label: "Estado *",
                    name: "state",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "state",
                    default: rooms || "",
                },
                {
                    label: "Delegación o Municipio *",
                    name: "delegation",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "delegation",
                    default: rooms || "",
                },
                {
                    label: "Número exterior *",
                    name: "n_exterior",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "n_exterior",
                    default: rooms || "",
                },
                {
                    label: "Número interior (Optional)",
                    name: "n_interior",
                    required: false,
                    type: "text",
                    width: 50,
                    subFormKey: "n_interior",
                    default: rooms || "",
                },
                {
                    label: "Piso / Nº de local / No. Consultorio",
                    name: "address",
                    required: true,
                    type: "text",
                    width: 50,
                    subFormKey: "address",
                    default: rooms || "",
                },
                {
                    label: "Teléfono de consultorio (Opcional)",
                    name: "phone",
                    required: false,
                    type: "text",
                    width: 50,
                    subFormKey: "phone",
                    default: rooms || "",
                },

                {
                    label: "Agrega el logotipo de su consultorio (opcional)",
                    name: "logo",
                    required: false,
                    type: "file",
                    width: 100,
                    subFormKey: "logo",
                    default: null,
                    maxFile: 1,
                },

                {
                    label: "Diseño de la Receta",
                    name: "title3",
                    required: true,
                    type: "title",
                    width: 100,
                },

                {
                    label: "Elija el diseño para su receta de las siguientes opciones:",
                    name: "design",
                    required: false,
                    type: "recetas",
                    width: 100,
                    subFormKey: "design",
                    default: rooms || "",
                    recetasOptions: [
                        { image: Receta1, value: "1" },
                        { image: Receta2, value: "2" },
                        { image: Receta3, value: "3" },
                    ],
                },
            ],
        },
    ];

    if (loading || loadingUpdate) return <Loading />;

    if (!rooms) return <> </>;
    return (
        <section className="  bg-[#fff] mt-5">
            <div className="flex   ">
                <div className=" w-full">
                    <FormGenerator
                        buttonText="Continuar"
                        submitData={submitData}
                        fields={fields}
                        loading={false}
                        renderButton={(handleSubmit) => (
                            <div className="flex justify-center w-full  ">
                                <button
                                    onClick={(e) => {
                                        e.preventDefault();
                                        handleSubmit();
                                    }}
                                    disabled={loadingUpdate}
                                    className="button-BlacK disabled:opacity-40 font-bold md:flex justify-center items-center border-black border-2 p-3 text-black rounded-lg w-60 mx-3 block my-4"
                                    type="submit"
                                >
                                    <FaSave
                                        color="#fbfbfb"
                                        className="me-3"
                                        size={20}
                                    />
                                    Guardar
                                </button>
                            </div>
                        )}
                    />
                </div>
            </div>
        </section>
    );
}
