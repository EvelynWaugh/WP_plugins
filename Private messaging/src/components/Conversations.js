import { useContext } from "react";
import styled from "styled-components";
import { MessageContext } from "../context/Context";
import SingleConversation from "./SingleConversation";
const Conversations = styled.div`
  min-width: 50%;
`;

export default function ConversationsContainer({ children, ...restProps }) {
  const { conversation } = useContext(MessageContext);
  return (
    <Conversations {...restProps}>
      {conversation?.conversations.map((conv) => (
        <SingleConversation
          name={conv.name}
          id={conv.id}
          key={conv.id}
          message={conv.message}
          time={conv.time}
        />
      ))}
    </Conversations>
  );
}
