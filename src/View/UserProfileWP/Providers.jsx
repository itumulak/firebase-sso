import Grid from "@mui/material/Grid";
import Box from '@mui/material/Box';
import Snackbar from "@mui/material/Snackbar";
import Alert from "@mui/material/Alert";
import CheckIcon from '@mui/icons-material/Check';
import facebookIconSvg from "../../assets/images/facebook-logo.svg";
import googleIconSvg from "../../assets/images/google-logo.svg";
import "../Frontend/assets/styles/login.css";
import { initializeApp } from "firebase/app";
import {
  getAuth,
  GoogleAuthProvider,
  FacebookAuthProvider,
  signOut,
  signInWithPopup,
} from "firebase/auth";
import { useState } from "react";

const firebaseConfig = firebase_sso_object.config;
initializeApp(firebaseConfig);

const auth = getAuth();

const linking = async (userId, provider) => {
    let signInProvider;

    switch (provider) {
        case "google":
            signInProvider = new GoogleAuthProvider();
            break;
        case "facebook":
            signInProvider = new FacebookAuthProvider();
    }

    const requestedLinked = await signInWithPopup(auth, signInProvider)
        .then((result) => {
            return result.user;
        })
        .catch((error) => {
            const errorCode = error.code;
            const errorMessage = error.message;

            console.log(errorCode, errorMessage);
        });
    
    if ( requestedLinked.accessToken ) {
        const uid = requestedLinked.uid;
        const formData = new FormData();
        formData.append("user_id", userId);
        formData.append("uid", uid);
        formData.append("provider", provider);
        formData.append("action", firebase_sso_object.action);
        formData.append("nonce", firebase_sso_object.nonce);

        return await fetch(firebase_sso_object.ajaxurl, {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        })
            .then((response) => {
                return response.json();
            })
            .then((response) => {
                if (response.success) return true;
                else return false;
            });
    }
}

const unlink = async (userId, provider) => {
    const formData = new FormData();
    formData.append("user_id", userId);
    formData.append("provider", provider);
    formData.append("action", firebase_sso_object.unlink_action);
    formData.append("nonce", firebase_sso_object.nonce);

    return await fetch(firebase_sso_object.ajaxurl, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
    })
        .then((response) => {
            return response.json();
        })
        .then((response) => {
            if (response.success) return true;
            else return false;
        })
}

const Providers = () => {
    const [successLinkDialog, setSuccessLinkDialog] = useState(false);
    const [successUnlinkDialog, setSuccessUnlinkDialog] = useState(false);

    const handleClick = async (event) => {
        event.preventDefault();
        const button = event.target;
        const action = button.getAttribute('data-action');
        const provider = button.getAttribute('data-provider');
        const userId = firebase_sso_object.user_id;

        if ( 'connect' === action ) {
            const linkStatus = await linking(userId, provider);

            console.log(linkStatus);

            if ( linkStatus ) {
                setSuccessLinkDialog(true);
                button.querySelector('span').innerHTML = 'Disconnect';
                button.setAttribute('data-action', 'disconnect');
            }
        }
        else if ( 'disconnect' === action ) {
            const unlinkStatus = await unlink(userId, provider);

            if ( unlinkStatus ) {
                setSuccessUnlinkDialog(true);
                button.querySelector('span').innerHTML = 'Connect';
                button.setAttribute('data-action', 'connect');
            }
        }
    }

    const handleOpenedDialog = (event, reason) => {
        if ( reason === 'clickway' ) {
            return;
        }

        setSuccessLinkDialog(false);
        setSuccessUnlinkDialog(false);
    }

    const facebook = (
        <button
            onClick={handleClick}
            id="provider-facebook" 
            data-provider="facebook" 
            data-action={firebase_sso_object.linked.facebook ? 'disconnect' : 'connect'} 
            className="providers__list-btn btn btn-facebook" style={{maxWidth: "280px"}}>
                <img height="24" src={facebookIconSvg} /> 
                <span>
                    {firebase_sso_object.linked.facebook ? 'Disconnect' : 'Connect'}
                </span>
        </button>
    
    );

    const google = (
        <button
            onClick={handleClick}
            id="provider-google" 
            data-provider="google" 
            data-action={firebase_sso_object.linked.google ? 'disconnect' : 'connect'} 
            className="providers__list-btn btn btn-google" style={{maxWidth: "280px"}}>
                <img height="18" src={googleIconSvg} /> 
                <span>
                    {firebase_sso_object.linked.google ? 'Disconnect' : 'Connect'}
                </span>
        </button>
    );

    return (
        <>
            <Box sx={{ flexGrow: 1 }}>
                <Grid container spacing={2}>
                    {firebase_sso_object.providers.google && <Grid item xs={3}>
                        {google}
                    </Grid>}
                    {firebase_sso_object.providers.facebook && <Grid item xs={3}>
                        {facebook}
                    </Grid>}
                </Grid>
            </Box>
            <Snackbar
                open={successLinkDialog}
                onClose={handleOpenedDialog}
                autoHideDuration={4000}
            >
                <Alert icon={<CheckIcon fontSize="inherit"/>} severity="success">
                    Successfully linked.
                </Alert>
            </Snackbar>
            <Snackbar
                open={successUnlinkDialog}
                onClose={handleOpenedDialog}
                autoHideDuration={4000}
            >
                <Alert icon={<CheckIcon fontSize="inherit"/>} severity="success">
                    Successfully unlinked.
                </Alert>
            </Snackbar>
        </>
    );
}

export default Providers;