// const { useState, createContext } = wp.element;
import { useState, createContext } from "react";

const MessageContext = createContext();

function MessageProvider({ children }) {
  const [conversation, setConversation] = useState(INBOX_EV);
  const [currUser, setCurrUser] = useState(USER);
  const [messages, setMessages] = useState(null);
  return (
    <MessageContext.Provider
      value={{ conversation, messages, setMessages, currUser }}
    >
      {children}
    </MessageContext.Provider>
  );
}
export { MessageContext, MessageProvider };
