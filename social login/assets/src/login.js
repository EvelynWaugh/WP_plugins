import { useCallback } from "react";
const { render } = wp.element;
import axios from "axios";
import { makeStyles } from "@material-ui/styles";
import { Button } from "@material-ui/core";
// import FacebookLogin from "react-facebook-login";
function Login() {
  const responseFacebook = (res) => {
    console.log(res);
  };
  const handleLogin = useCallback(async (networkName) => {
    // e.preventDefault();
    // const url = LOGIN.admin_url;
    // const params = new URLSearchParams({
    //   action: "login_endpoint",
    //   nonce: LOGIN.nonce,
    //   network: "facebook",
    //   process: "login",
    //   token: "555555",
    // });
    if (networkName === "facebook") {
      await FB.login((response) => {
        const url = LOGIN.admin_url;
        const params = new URLSearchParams({
          action: "login_endpoint",
          nonce: LOGIN.nonce,
          network: networkName,
          process: "login",
          token: response.authResponse.accessToken,
        });
        console.log(response);
        axios
          .post(url, params, {
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
          })
          .then((res) => {
            console.log(res.data);
            window.location.href = "http://localhost/tennis/wp-admin/";
            // window.location.reload();
          })
          .catch((err) => {
            console.log(err.message);
          });
      });
    } else {
      gapi.load("auth2", function () {
        gapi.auth2.init();
        console.log(gapi.auth2.getAuthInstance());
        gapi.auth2
          .getAuthInstance()
          .attachClickHandler(
            document.getElementById("google_login_button_ev"),
            {},
            function (response) {
              const url = LOGIN.admin_url;
              const params = new URLSearchParams({
                action: "login_endpoint",
                nonce: LOGIN.nonce,
                network: networkName,
                process: "login",
                token: response.getAuthResponse().id_token,
              });
              console.log(response);
              axios
                .post(url, params, {
                  headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                  },
                })
                .then((res) => {
                  console.log(res.data);
                  // window.location.href = "http://localhost/tennis/wp-admin/";
                  // window.location.reload();
                })
                .catch((err) => {
                  console.log(err.message);
                });
            }
          );
      });
    }
  });

  return (
    <div>
      {/* <FacebookLogin
        appId="382635072932998"
        autoLoad={false}
        fields="name,email,picture"
        callback={responseFacebook}
        cssClass="ev-facebook-button"
        icon="fa-facebook"
      /> */}
      <Button
        color="primary"
        variant="contained"
        onClick={() => handleLogin("facebook")}
      >
        Fb login
      </Button>

      <Button
        id="google_login_button_ev"
        color="primary"
        variant="contained"
        onClick={() => handleLogin("google")}
      >
        Google login
      </Button>
    </div>
  );
}

render(<Login />, document.getElementById("social_login_wrapper"));
