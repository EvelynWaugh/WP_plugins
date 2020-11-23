import { useContext, useEffect } from "react";
import styled from "styled-components";
import { useLocation } from "react-router-dom";
import { MessageContext } from "../context/Context";
import { setInput } from "../hooks/inputHook";
import axios from "axios";

const MessageBox = styled.div`
  display: flex;
  flex-direction: column;
  justify-content: ${(props) =>
    props.owner === "true" ? "flex-start" : "flex-end"};
  text-align: ${(props) => (props.owner === "true" ? "left" : "right")};

  padding: 10px;
  margin-bottom: 5px;
  border-bottom: 1px solid #333;
`;
const MessageHeader = styled.div`
  display: flex;
  margin-left: ${(props) => (props.owner === "true" ? "" : "auto")};
  padding: 20px;
`;
const MessageBody = styled.div``;

const MessageBottom = styled.div``;
const MessageInput = styled.input``;

export default function MessageContainer(props) {
  const [input, reset, saveInputValue] = setInput({
    message: "",
  });
  const { messages, setMessages, currUser, conversation } = useContext(
    MessageContext
  );
  const location = useLocation();
  console.log(location.search);

  const convid = new URLSearchParams(location.search).get("conversation");

  useEffect(() => {
    const url = API_EV.ajaxUrl;
    const params = new URLSearchParams({
      action: "grab_conv_message",
      convId: convid,
    });
    axios
      .post(url, params, {
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          // "X-WP-Nonce": api.nonce,
        },
      })
      .then((res) => {
        setMessages(res.data);
        console.log(res.data);
      })
      .catch((error) => console.log(error.message));
  }, [convid]);

  // const getCurrConv = conversation?.conversations.filter(
  //   (conv) => conv.conv_id === convid
  // );
  // const current_user = currUser.user.id;
  // const reciever_user =
  //   current_user == getCurrConv[0].sender
  //     ? getCurrConv[0].reciever
  //     : getCurrConv[0].sender;
  // console.log(getCurrConv, current_user, parseInt(reciever_user));
  function postPrMessage(e) {
    e.preventDefault();
    const url = API_EV.ajaxUrl;
    const current_user = currUser.user.id;
    const getCurrConv = conversation?.conversations.filter(
      (conv) => conv.id === convid
    );
    const reciever_user =
      current_user == getCurrConv[0].sender
        ? getCurrConv[0].reciever
        : getCurrConv[0].sender;
    const params = new URLSearchParams({
      action: "post_private_message",
      message: input.message,
      convid: convid,
      sender_id: parseInt(current_user),
      reciever_id: parseInt(reciever_user),
    });
    axios
      .post(url, params, {
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      })
      .then((res) => {
        console.log(res.data);
        setMessages({
          ...messages,
          messages: [...messages.messages, res.data.message],
        });
        // console.log(messages);
        // console.log(messages.messages);
        // console.log({
        //   ...messages,
        //   messages: [...messages.messages, res.data.message],
        // });
        reset("message");
      })
      .catch((err) => console.log(err.message));
  }
  return (
    <div>
      {messages?.messages?.map((message) => (
        <MessageBox owner={message.owner}>
          <MessageHeader>
            <img src={message.pic} />
            <div>{message.sender_name}</div>
          </MessageHeader>
          <MessageBody>{message.message}</MessageBody>
        </MessageBox>
      ))}

      <MessageBottom>
        <form onSubmit={postPrMessage}>
          <MessageInput
            type="text"
            name="message"
            value={input.message}
            onChange={saveInputValue}
          />
        </form>
      </MessageBottom>
    </div>
  );
}
