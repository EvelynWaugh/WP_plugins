import { useEffect, useCallback, useContext } from "react";
import styled from "styled-components";
import { useHistory } from "react-router-dom";
import { MessageContext } from "../context/Context";
import axios from "axios";
const ConvBox = styled.div`
  border: 3px solid #000;
`;
const HeaderConv = styled.div`
  display: flex;
  padding: 10px;
  justify-content: space-between;
  background-color: #eee;
`;
const BodyConv = styled.div`
  padding: 10px;
`;

export default function SingleConversation({ children, ...restProps }) {
  const { messages, setMessages } = useContext(MessageContext);
  const history = useHistory();
  const url = API_EV.ajaxUrl;

  //   useEffect(() => {
  //     console.log("render");
  //     const url = API_EV.ajaxUrl;
  //     const params = new URLSearchParams();
  //     params.append("action", "grab_conv_message");
  //     params.append("convId", restProps.id);
  //     axios
  //       .post(url, params, {
  //         headers: {
  //           "Content-Type": "application/x-www-form-urlencoded",
  //           // "X-WP-Nonce": api.nonce,
  //         },
  //       })
  //       .then((res) => console.log(res.data))
  //       .catch((error) => console.log(error.message));

  //     // fetch(url, {
  //     //   method: "POST",
  //     //   body: JSON.stringify(data),
  //     //   headers: {
  //     //     "Content-Type": "application/json",
  //     //   },
  //     // })
  //     //   .then((res) => res.json())
  //     //   .then((data) => console.log(data));
  //   }, []);

  //   const pushToConversation = () => {
  //     history.push(`?conversation=${restProps.id}`);
  //     console.log(history);
  //   };

  const pushToConversation = useCallback(async () => {
    const params = new URLSearchParams();
    params.append("action", "grab_conv_message");
    params.append("convId", restProps.id);
    history.push(`?conversation=${restProps.id}`);
    console.log(history);
    // await axios
    //   .post(url, params, {
    //     headers: {
    //       "Content-Type": "application/x-www-form-urlencoded",
    //       // "X-WP-Nonce": api.nonce,
    //     },
    //   })
    //   .then((res) => {
    //     setMessages(res.data);
    //     console.log(res.data);
    //   })
    //   .catch((error) => console.log(error.message));
    console.log("Clicked");
  }, [restProps.id, messages]);
  return (
    <ConvBox onClick={pushToConversation}>
      <HeaderConv>
        <div>{restProps.name}</div>
        <div>{restProps.time}</div>
      </HeaderConv>
      <BodyConv>
        <div>{restProps.message}</div>
      </BodyConv>
    </ConvBox>
  );
}
