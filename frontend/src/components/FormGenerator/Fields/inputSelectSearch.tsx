import { Field } from "@/types/Generals/FormGenerator";
import React from "react";
import Select from "react-select";

export default function InputSelectSearch({
  label,
  name,
  error,
  options,
  setValue,
  customChange,
  watch,
  disabled,
  width = 100,
  ModalNotFound,
  NotFound,
}: Field) {
  const values: any = watch();
  const [show, setShow] = React.useState(false);
  const [inputValue, setInputValue] = React.useState(values[name]);
  const closeModal = () => {
    setShow(false);
  };

  const onChange = (selectedOption: any) => {
    setValue(name, selectedOption?.value || "");
    if (customChange)
      customChange(selectedOption?.value || "", setValue, values);
  };

  const customStyles = {
    control: (base: any) => ({
      ...base,
      height: "50px",
      minHeight: "50px",
      ...(error && { borderColor: "red" }),
    }),
  };

  const customOptions = [
    ...(options || []),
    { label: values[name], value: values[name] },
  ];

  const handleAccept = (value: string) => {
    setValue(name, value);
  };

  return (
    <div className="px-2 full-width" style={{ width: `${width}%` }}>
      <label
        className={`${error && "text-red-400"} title-form-generator `}
        htmlFor={name}
      >
        {label}
      </label>

      {show && (
        <ModalNotFound
          show={show}
          closeModal={closeModal}
          handleAccept={handleAccept}
        />
      )}

      <Select
        id={name}
        name={name}
        options={customOptions}
        isDisabled={disabled}
        value={customOptions?.find((option) => option.value === values[name])}
        className={`disabled:opacity-70 my-2 `}
        classNamePrefix={error ? "react-select-error" : "react-select"}
        onChange={onChange}
        onInputChange={(value) => {
          if (value) {
            setInputValue(value);
          }
          return value;
        }}
        noOptionsMessage={() => (
          <NotFound setShow={() => setShow(inputValue)} />
        )}
        isClearable
        isSearchable
        styles={customStyles}
      />
    </div>
  );
}
