(function ($) {
  $("#ev_chatbox").on("click", function () {
    const data = {
      action: "grab_conv_message",
      convId: 1,
    };
    const params = new URLSearchParams();
    params.append("action", "grab_conv_message");
    params.append("convId", "1");
    $.ajax({
      url: API_EV.ajaxUrl,
      method: "POST",
      data: { action: "grab_conv_message", convId: 1 },
    })
      .then((res) => console.log(res))
      .fail((err) => console.log(err.message));
  });

  //   fetch(API_EV.ajaxUrl, {
  //     method: "POST",
  //     credentials: "same-origin",
  //     headers: {
  //       "Content-Type": "application/x-www-form-urlencoded",
  //     },
  //     body: params,
  //   })
  //     .then((res) => res.text())
  //     .then((data) => console.log(data));
  // });
})(jQuery);
