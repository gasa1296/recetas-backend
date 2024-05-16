import { useRecipeStore } from "@/store/recipes";
import { MdOutlineLocalPrintshop } from "react-icons/md";

interface IRecipe {
  recipes: any;
}

export function Recipe({ recipes }: IRecipe) {
  const { handlePrint } = useRecipeStore((state) => ({
    handlePrint: state.handlePrint,
  }));

  const handlePrintAll = async () => {
    for (const recipe of recipes) {
      if (recipe.hasSign) return;

      await handlePrint(recipe.id, recipe.document_id);
    }
  };
  return (
    <div className="">
      <div className=" flex flex-wrap justify-center items-center ">
        <button
          onClick={handlePrintAll}
          className="flex flex-wrap justify-center items-center border button-print  mw-[15%] mx-3 text-[20px] mt-4 p-1 px-10"
        >
          <MdOutlineLocalPrintshop size={18} />
          <p className="mx-2 "> Imprimir / Visualizar PDF</p>
        </button>
      </div>
    </div>
  );
}
