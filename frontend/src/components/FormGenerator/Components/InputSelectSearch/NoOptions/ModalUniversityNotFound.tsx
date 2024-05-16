import React from "react";

export default function ModalUniversityNotFound({
  show,
  closeModal,
  handleAccept,
}: any) {
  const [inputText, setInputText] = React.useState(show || "");

  const handleSubmit = (e: any) => {
    e.preventDefault();
    handleAccept(inputText);
    closeModal();
    setInputText("");
  };

  return (
    <>
      {show && (
        <div className="fixed inset-0 z-[101] bg-gray-800 bg-opacity-75 transition-opacity ">
          <div className="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div className="flex min-h-full items-end justify-center p-2 text-center sm:items-center sm:p-0">
              <div className="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <div className="flex justify-between px-3 py-3 ">
                  <p className="text-[#4B4B4B] font-bold text-[20px]">
                    Crear nueva universidad
                  </p>
                  <p
                    onClick={(e) => {
                      e.preventDefault();
                      closeModal();
                    }}
                    className="text-[#4B4B4B] font-bold text-[18px] cursor-pointer"
                  >
                    Cerrar
                  </p>
                </div>
                <div className="bar-lateral"> </div>

                <div className="bg-white px-2 pb-4 pt-5 sm:p-2 sm:pb-4">
                  <form className="mt-4 px-2" onSubmit={handleSubmit}>
                    <label
                      className={`  flex relative text-[#1A1A1A] text-[16px] `}
                    >
                      Nombre de la universidad:
                    </label>

                    <input
                      type="text"
                      value={inputText}
                      onChange={(e) => setInputText(e.target.value)}
                      className={`w-full form-control my-2 text-[16px] m-0 p-3 rounded-md border-[#DBE2EA] border-2 focus:outline-none`}
                    />

                    <div className=" w-full  ">
                      <div className="bar-lateral mt-4 " />

                      <div className=" px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button
                          onClick={handleSubmit}
                          type="submit"
                          className="inline-flex w-full justify-center rounded-md px-3 py-2 button-green text-sm font-semibold text-white shadow-sm hover:bg-gray-50 sm:ml-3 sm:w-auto"
                        >
                          Guardar
                        </button>
                        <button
                          type="submit"
                          className="mt-3 inline-flex w-full justify-center rounded-md button-edit px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50  sm:mt-0 sm:w-auto"
                          onClick={(e) => {
                            e.preventDefault();
                            closeModal();
                          }}
                        >
                          Cancelar
                        </button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
