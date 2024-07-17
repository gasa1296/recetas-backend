import Loading from "@/components/Loading";
import { sendRecipeByWhatsapp } from "@/services/recipes";
import { useRecipeStore } from "@/store/recipes";
import { FaRegEnvelope, FaWhatsapp } from "react-icons/fa";
import { MdOutlineLocalPrintshop } from "react-icons/md";

interface IRecipe {
  groupType: string;
  id: string;
  hasSign: boolean;
  enableDownload: boolean;
  document_id: string;
}

export function RecipeSign({
  groupType,
  id,
  hasSign,
  enableDownload,
  document_id,
}: IRecipe) {
  const { handlePrint, SendRecipeByEmail, sendRecipeByWhatsapp } =
    useRecipeStore((state) => ({
      handlePrint: state.handlePrint,
      sendRecipeByWhatsapp: state.SendRecipeByWhatsapp,
      SendRecipeByEmail: state.SendRecipeByEmail,
    }));

  return (
    <div className="">
      <p className="text-[#1A1A1A] text-[20px] font-bold"></p>
      {!enableDownload && hasSign ? (
        <div className="pt-5">
          <Loading
            text="Estamos certificando la receta con firma electrónica, un momento por favor"
            textSize={20}
            minHeight={200}
          />
        </div>
      ) : (
        <div className=" flex flex-wrap justify-center items-center ">
          <button
            onClick={() => handlePrint(id, document_id)}
            className="flex  justify-center items-center border button-print  mw-[15%] mx-3 text-[20px] mt-4 p-1 px-10"
          >
            <MdOutlineLocalPrintshop size={18} />
            <p className="mx-2 "> Imprimir / Visualizar PDF</p>
          </button>
          {hasSign && (
            <>
              <button
                onClick={() => SendRecipeByEmail(id)}
                className="flex items-center justify-center button-white mw-[20%] p-1 px-10 mx-3 mt-4"
              >
                <FaRegEnvelope color="#1A1A1A " size={18} />
                <p className="mx-2 "> Enviar por correo</p>
              </button>

              <button
                onClick={() => sendRecipeByWhatsapp(id)}
                className="flex items-center border justify-center button-whatsapp   mw-[15%] text-[20px] mt-4 p-1 mx-3 px-10"
              >
                <FaWhatsapp color="white" size={20} className="" />
                <p className="mx-2 "> Enviar por Whatsapp</p>
              </button>
            </>
          )}
        </div>
      )}
    </div>
  );
}
