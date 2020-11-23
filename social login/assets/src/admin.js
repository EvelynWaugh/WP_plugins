import React, { useState, useEffect, useCallback } from "react";
import axios from "axios";
const { render } = wp.element;
import CssBaseline from "@material-ui/core/CssBaseline";

import Container from "@material-ui/core/Container";
import Switch from "@material-ui/core/Switch";
import Card from "@material-ui/core/Card";
import FormGroup from "@material-ui/core/FormGroup";
import FormControlLabel from "@material-ui/core/FormControlLabel";
import { styled, useTheme, makeStyles } from "@material-ui/core/styles";
import {
  CircularProgress,
  Button,
  ButtonGroup,
  Box,
  Typography,
  TextField,
  FormControl,
  Fab,
} from "@material-ui/core";

import FacebookIcon from "@material-ui/icons/Facebook";
import GTranslateSharpIcon from "@material-ui/icons/GTranslateSharp";

const TransTextField = styled(({ checked, ...others }) => (
  <TextField {...others} />
))({
  height: (props) => (props.checked === "on" ? "100%" : "0px"),
  minHeight: (props) => (props.checked === "on" ? "100%" : "0px"),
  opacity: (props) => (props.checked === "on" ? "1" : "0"),
  visibility: (props) => (props.checked === "on" ? "visible" : "hidden"),
  transition: "all 0.3s ease",
});

const SocialButton = styled(({ checked, ...others }) => <Button {...others} />)(
  {
    opacity: (props) => (props.checked === "on" ? "1" : "0"),
    visibility: (props) => (props.checked === "on" ? "visible" : "hidden"),
    transition: "all 0.3s ease",
  }
);
const useStyles = makeStyles((theme) => ({
  buttonGroup: {
    marginTop: "100px",
  },
  saveButton: {
    backgroundColor: theme.palette.secondary.dark,
    color: "#fff",
    "&:hover": {
      backgroundColor: theme.palette.secondary.light,
    },
  },
}));
function App() {
  const theme = useTheme();
  const classes = useStyles();
  // const classes = useStyles();
  const admin_notifications = {
    success: "Сохранено",
    error: "Ошибка, повторите действие",
  };

  const [socials, setSocials] = useState({
    options: OPTIONS,
  });
  const [isLoading, setIseLoading] = useState(false);
  const [notifications, setNotifications] = useState(null);
  console.log(socials);

  const handleChange = (event) => {
    setSocials({
      ...socials,
      options: {
        ...socials.options,
        social_login: {
          ...socials.options.social_login,
          [event.target.name]:
            socials.options.social_login[event.target.name] === "on"
              ? "off"
              : "on",
        },
      },
    });
  };
  const saveApi = (e) => {
    setSocials({
      ...socials,
      options: {
        ...socials.options,
        social_login: {
          ...socials.options.social_login,
          [e.target.name]: e.target.value,
        },
      },
    });
  };
  const handleConnect = useCallback(async (e) => {
    await FB.login((response) => {
      const url = EV_PARAMS.admin_url;
      const data = {
        action: "login_endpoint",
        network: "facebook",
        token: response.authResponse.accessToken,
        process: "connect",
        nonce: socials.options.nonce,
      };
      console.log(response);
      const params = new URLSearchParams(data);
      axios
        .post(url, params, {
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
        })
        .then((res) => console.log(res.data))
        .catch((err) => console.log(err.message));
    });
  });
  const saveOptions = useCallback(async (e) => {
    e.preventDefault();
    const url = EV_PARAMS.admin_url;
    const data = {
      action: "social_login_params",
      nonce: socials.options.nonce,
      ...socials.options.social_login,
    };
    // const params = new URLSearchParams({
    //   action: "social_login_params",
    //   facebook: socials.options.social_login.facebook,
    //   google: socials.options.social_login.google,
    //   data: socials.options.social_login,
    //   nonce: socials.options.nonce,
    // });
    const params = new URLSearchParams(data);

    setIseLoading(true);
    await axios
      .post(url, params, {
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          // "X-WP-Nonce": api.nonce,
        },
      })
      .then((res) => {
        console.log(res);
        setNotifications({
          success: "Сохранено",
        });
        setTimeout(() => {
          setNotifications(null);
        }, 2000);

        setIseLoading(false);
      })
      .catch((err) => {
        setNotifications({
          error: err.message,
        });
        setTimeout(() => {
          setNotifications(null);
        }, 2000);
        setIseLoading(false);
      });
  });

  return (
    <>
      <CssBaseline />
      <Container>
        <form onSubmit={saveOptions}>
          <FormGroup row>
            <FormControlLabel
              control={
                <Switch
                  checked={
                    socials.options.social_login.facebook === "on"
                      ? true
                      : false
                  }
                  onChange={handleChange}
                  name="facebook"
                />
              }
              label="Use Facebook Login"
            />
            <FormControlLabel
              control={
                <Switch
                  checked={
                    socials.options.social_login.google === "on" ? true : false
                  }
                  onChange={handleChange}
                  name="google"
                />
              }
              label="Use Google Login"
            />
          </FormGroup>
          <FormGroup row>
            <FormControl>
              <TransTextField
                label="FB app id"
                checked={socials.options.social_login.facebook}
                name="facebook_api"
                onChange={saveApi}
                value={socials.options.social_login.facebook_api}
              />
              <SocialButton
                variant="contained"
                className={classes.saveButton}
                checked={socials.options.social_login.facebook}
                // color={theme.palette.secondary.dark}
                size="small"
                endIcon={<FacebookIcon />}
                onClick={handleConnect}
              >
                Connect Facebook
              </SocialButton>
            </FormControl>
            <FormControl>
              <TransTextField
                label="Google app id"
                checked={socials.options.social_login.google}
                name="google_api"
                onChange={saveApi}
                value={socials.options.social_login.google_api}
              />
              <SocialButton
                variant="contained"
                className={classes.saveButton}
                checked={socials.options.social_login.google}
                // color={theme.palette.secondary.dark}
                size="small"
                endIcon={<GTranslateSharpIcon />}
                onClick={handleConnect}
              >
                Connect Google
              </SocialButton>
            </FormControl>
          </FormGroup>
          <FormGroup className={classes.buttonGroup}>
            <ButtonGroup>
              <Button variant="contained" color="primary" type="submit">
                Сохранить
              </Button>
              {isLoading && (
                <CircularProgress variant="indeterminate" color="secondary" />
              )}
            </ButtonGroup>
          </FormGroup>
        </form>
        {notifications !== null && (
          <Box>
            <Typography color="green">
              {notifications.success || notifications.error}
            </Typography>
          </Box>
        )}
      </Container>
    </>
  );
}
render(<App />, document.getElementById("social_login"));
