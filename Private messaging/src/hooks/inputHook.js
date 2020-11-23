import { useState } from "react";

function setInput(value) {
  const [input, setInput] = useState(value);

  function saveInputValue(e) {
    setInput({
      [e.target.name]: e.target.value,
    });
  }
  function reset(name) {
    setInput({
      [name]: "",
    });
  }
  return [input, reset, saveInputValue];
}
export { setInput };
