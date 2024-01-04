import React, { useState } from 'react'
import { FaRegUser, FaUser, FaPills } from "react-icons/fa";
import { MdOutlineArrowBackIos } from "react-icons/md";
import FormGenerator from '@/components/FormGenerator';
import { Field } from '@/types/Generals/FormGenerator';
import SearchInput from '../SearchMedicine/SearchInput';
import ResultMedicine from '../SearchMedicine/ResultMedicine';
import FindMedicine from '../SearchMedicine/FindMedicine';
import AddMedicationPrescription from './AddMedicationPrescription';

export default function AddMedicine({ nextStep, backStep }: any) {
    const submitData = async () => {

        nextStep();

    };
    const [show, setShow] = useState(true)
    const fields: Field[] = [
        {
            label: "Paciente",
            name: "confirmarContrasena",
            required: true,
            type: "title",
        },
        {
            label: "Nombre",
            name: "name-Patient",
            required: true,
            type: "text",
            width: 48,
            disabled: true,

        },
        {
            label: "Correo electrónico",
            name: "email",
            required: true,
            type: "email",
            width: 48,
            disabled: true,

        },

    ];
    return (
        <section>
            <div className='flex items-center  mb-4 p-2 ps-3 container-dashboard'>

                <button onClick={() => { nextStep() }} className='button-BlacK flex justify-center items-center p-2 w-[120px] '><MdOutlineArrowBackIos size={20} /> <p className='ms-1'> Regresar</p>    </button>
            </div>
            <FormGenerator
                submitData={submitData}
                fields={fields}
                loading={false}
                renderButton={(handleSubmit) => (
                    <div className="flex justify-center w-full  ">

                    </div>
                )}
            />

            <div className='flex items-center  border-Tab p-2 ps-3 mt-4'>
                <FaPills color="#Fff " size={28} />
                <p className='text-[#fff] text-[26px] ms-3'>Medicamento</p>
            </div>
            <section className='container-Patiens  flex justify-center py-5 ' >
                <div className='w-[90%]'>

                    <SearchInput />
                    <div className='mt-5 container-AddMdicine'>
                        <AddMedicationPrescription />
                    </div>
                </div>


            </section>
        </section>
    )
}
