const { render, useState } = wp.element;

import { BrowserRouter as Router } from "react-router-dom";
import { MessageProvider } from "./context/Context";
import ChatboxContainer from "./components/Chatbox";
import ConversationsContainer from "./components/Conversations";
import MessageContainer from "./components/Messages";
function App() {
  const [users, setUsers] = useState(USERS_EV.users);
  const [api, setApi] = useState(API_EV);
  const [user, setUser] = useState(USER);

  const [status, setStatus] = useState(false);
  console.log(INBOX_EV);
  const handleClick = () => {
    setStatus(!status);
  };
  return (
    <Router>
      <MessageProvider>
        <ChatboxContainer>
          <ConversationsContainer></ConversationsContainer>
          <MessageContainer></MessageContainer>
        </ChatboxContainer>
      </MessageProvider>
    </Router>
  );
}

render(<App />, document.getElementById("ev_chatbox"));
