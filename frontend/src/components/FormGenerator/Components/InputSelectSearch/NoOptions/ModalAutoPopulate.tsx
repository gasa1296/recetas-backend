import React from "react";

interface Props {
  show: boolean;
  closeModal: () => void;
  options: any[];
  onSelect: (option: any) => void;
}

export default function ModalAutoPopulate({
  show,
  closeModal,
  options,
  onSelect,
}: Props) {
  const [selectedOption, setSelectedOption] = React.useState<any>(null);
  const [searchTerm, setSearchTerm] = React.useState("");

  const handleAccept = () => {
    if (selectedOption) {
      selectedOption.value.selectedEmail = selectedOption.selectedEmail;
      onSelect(selectedOption);
      closeModal();
    }
  };

  const filteredOptions = options.filter(
    (option) =>
      option.label.toLowerCase().includes(searchTerm.toLowerCase()) ||
      option.cedulas.toLowerCase().includes(searchTerm.toLowerCase()) ||
      option.email.some((email: string) =>
        email.toLowerCase().includes(searchTerm.toLowerCase())
      )
  );

  return (
    <>
      {show && (
        <div className="fixed inset-0 z-[1001] overflow-y-auto bg-gray-800 bg-opacity-75 transition-opacity">
          <div className="fixed inset-0 z-10 w-screen h-screen">
            <div className="flex min-h-screen items-end justify-center p-2 text-center sm:items-center sm:p-0">
              <div className="relative transform rounded-lg bg-white overflow-hidden text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-6xl max-h-[90vh] overflow-y-auto">
                <div className="flex justify-between px-6 py-4">
                  <p className="text-[#4B4B4B] font-bold text-[20px]">
                    Selecciona el médico
                  </p>
                </div>
                <div className="bar-lateral"></div>

                <div className="mt-4 px-6">
                  <div className="mb-4">
                    <input
                      type="text"
                      placeholder="Buscar médico..."
                      className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      value={searchTerm}
                      onChange={(e) => setSearchTerm(e.target.value)}
                    />
                  </div>

                  <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200">
                      <thead className="bg-gray-50">
                        <tr>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre
                          </th>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cedulas
                          </th>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                          </th>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente Ecommerce
                          </th>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acción
                          </th>
                        </tr>
                      </thead>
                      <tbody className="bg-white divide-y divide-gray-200">
                        {filteredOptions.map((option, index) => (
                          <tr
                            key={index}
                            className={`hover:bg-gray-50 cursor-pointer ${
                              selectedOption?.value === option.value
                                ? "bg-blue-50"
                                : ""
                            }`}
                            onClick={() => {
                              const newSelectedOption = { ...option };
                              if (
                                Array.isArray(option.email) &&
                                option.email.length > 0
                              ) {
                                newSelectedOption.selectedEmail =
                                  option.email[0];
                              }
                              setSelectedOption(newSelectedOption);
                            }}
                          >
                            <td className="px-6 py-4 whitespace-nowrap">
                              <div className="text-sm font-medium text-gray-900">
                                {option.label}
                              </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                              <div className="text-sm text-gray-500">
                                {option.cedulas || "No especificada"}
                              </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap  max-w-[350px]">
                              {selectedOption?.value === option.value &&
                              Array.isArray(option.email) &&
                              option.email.length > 1 ? (
                                <select
                                  className="w-full px-2 py-1 border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-blue-500 focus:border-blue-500"
                                  value={selectedOption.selectedEmail || ""}
                                  onChange={(e) => {
                                    setSelectedOption({
                                      ...selectedOption,
                                      selectedEmail: e.target.value,
                                    });
                                  }}
                                  onClick={(e) => e.stopPropagation()}
                                >
                                  {option.email.map(
                                    (email: string, index: number) => (
                                      <option key={index} value={email}>
                                        {email}
                                      </option>
                                    )
                                  )}
                                </select>
                              ) : (
                                <div className="text-sm text-gray-500 text-wrap truncate">
                                  {option.email.join(", ") || "No especificada"}
                                </div>
                              )}
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                              <div className="text-sm text-gray-500">
                                {option.clienteEcommerce || ""}
                              </div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                              <button
                                className={`px-3 py-1 rounded-full text-sm ${
                                  selectedOption?.value === option.value
                                    ? "bg-blue-500 text-white"
                                    : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                                }`}
                              >
                                {selectedOption?.value === option.value
                                  ? "Seleccionado"
                                  : "Seleccionar"}
                              </button>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>

                  <div className="w-full">
                    <div className="px-0 py-4 flex justify-end items-center gap-3 mt-4 border-t border-gray-200">
                      <button
                        type="button"
                        className="px-4 py-2 bg-gray-100 text-gray-700 font-medium text-sm rounded hover:bg-gray-200 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300"
                        onClick={(e) => {
                          e.preventDefault();
                          closeModal();
                        }}
                      >
                        Cancelar
                      </button>
                      <button
                        onClick={handleAccept}
                        disabled={!selectedOption}
                        className="px-4 py-2 bg-gray-800 text-white font-medium text-sm rounded transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-300"
                      >
                        Cargar información
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
