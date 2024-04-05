import useCustomEffect from "@/hooks/useCustomEffect";
import { useRoomsStore } from "@/store/rooms";
import React, { useEffect } from "react";
import { Field } from "@/types/Generals/FormGenerator";

export default function InputRoom({
  register,

  name,
  required,
  error,
  disabled,
  watch,

  width = 100,
}: Field) {
  const GetRooms = useRoomsStore((state) => state.GetRooms);
  const rooms = useRoomsStore((state) => state.rooms);
  useCustomEffect({ requestGet: GetRooms });

  const values: any = watch();

  useEffect(() => {
    if (rooms && rooms?.length === 1 && !values[name]) {
      const firstRoomInput = document.getElementById("room-1");
      if (firstRoomInput) {
        firstRoomInput.click();
      }
    }
  }, [rooms]);

  return (
    <div style={{ width: `${width}%` }} className="px-2 pt-6 full-width">
      <label
        className={`${
          error && "text-red-400"
        }  text-[20px] text-[#1A1A1A] font-bold my-4 pt-4`}
      >
        Selecciona el consultorio para generar tu receta electrónica
      </label>
      <section className="flex flex-wrap justify-start">
        {rooms?.map((room: any, index: number) => (
          <div className="flex items-start card-sing w-[100%] md:w-[40%] md:min-w-[400px] p-2 md:mr-5 mt-5">
            <input
              type="radio"
              id={`room-${index + 1}`}
              className="form-check-input mt-1"
              value={room.id}
              disabled={disabled}
              name="room"
              {...register(name, { required })}
            />
            <label className="ms-4" htmlFor={`room-${index + 1}`}>
              <h5 className="title-card">{room.name}</h5>
              <p className="text-card">{room.address}</p>
              <p className="text-card">{room.street}</p>
              <p className="text-card">{room.state}</p>
            </label>
          </div>
        ))}
      </section>
    </div>
  );
}
