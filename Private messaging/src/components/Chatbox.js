import styled from "styled-components";

const Chatbox = styled.div`
  max-width: 600px;
  margin: 0 auto;
  display: flex;
`;

export default function ChatboxContainer({ children, ...restProps }) {
  return <Chatbox {...restProps}>{children}</Chatbox>;
}
