import React, { useState, useRef } from "react";
/* import { Overlay } from "react-bootstrap"; */
import { FaLightbulb } from "react-icons/fa";
import { BiXCircle } from "react-icons/bi";

interface IProps {
  tooltip: {
    title: string;
    content: string;
  };
}

export default function Tooltip({ tooltip }: IProps) {
  const [show, setShow] = useState(false);
  const target = useRef(null);
  return (
    <>
      <div ref={target} onClick={() => setShow(!show)}>
        <FaLightbulb />
      </div>
      {/*  <Overlay target={target.current} show={show} placement="bottom">
        {({
          placement: _placement,
          arrowProps: _arrowProps,
          show: _show,
          popper: _popper,
          hasDoneInitialMeasure: _hasDoneInitialMeasure,
          ...props
        }) => (
          <div
            {...props}
            style={{
              position: "absolute",
              backgroundColor: "#f8f8f8",
              padding: "10px",
              paddingRight: 30,
              color: "black",
              maxWidth: 300,
              borderRadius: 3,
              ...props.style,
            }}
          >
            <div
              onClick={() => setShow(false)}
              style={{
                position: "absolute",
                top: 7,
                right: 10,
                cursor: "pointer",
              }}
            >
              <BiXCircle size={20} />
            </div>

            <p style={{ fontWeight: 600 }}>{tooltip.title}</p>
            <span>{tooltip.content}</span>
          </div>
        )}
      </Overlay> */}
    </>
  );
}
