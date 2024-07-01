import Stack from "@mui/material/Stack"
import TextField from "@mui/material/TextField";
import Button from '@mui/material/Button';
import Snackbar from '@mui/material/Snackbar';
import Alert from '@mui/material/Alert';
import CheckIcon from '@mui/icons-material/Check';
import { useState } from "react";

const Configuration = () => {
    const [apiKey, setApiKey] = useState('');
    const [authDomain, setAuthDomain] = useState('');
    const [successDialog, setSuccessDialog] = useState(false);

    const handleSubmit = async (event) => {
        event.preventDefault();

        const formData = new FormData();
        formData.append('apiKey', apiKey);
        formData.append('authDomain', authDomain);
        formData.append('action', sso_object.config_action);
        formData.append('nonce', sso_object.nonce);

        handleOpenSucessDialog();

        // await fetch(sso_object.ajaxurl, {
        //     method: "POST",
        //     body: formData,
        //     credentials: "same-origin",
        // })
        //     .then((response) => {
        //         return response.json();
        //     })
        //     .then((data) => {
        //         // add pop up notice like toast jquery.
        //     });
    }

    const handleOpenSucessDialog = () => {
        setSuccessDialog(true);
    }

    const handleCloseSuccessDialog = (event, reason) => {
        if ( reason === 'clickaway' ) {
            return;
        }

        setSuccessDialog(false);
    }

    return (
        <form onSubmit={handleSubmit}>
            <Stack spacing={2}>
                <h1>Firebase Configurations</h1>
                <p>Get a copy, and paste your <a href="https://firebase.google.com/docs/web/setup?authuser=0#config-object">Firebase config object</a> found at your project settings.</p>
                <TextField 
                    id="apiKey" 
                    label="API Key" 
                    variant="standard"
                    type="password"
                    onInput={(e) => setApiKey(e.target.value)}
                />
                <TextField 
                    id="authDomain" 
                    label="Authorized Domain" 
                    variant="standard"
                    type="password"
                    onInput={(e) => setAuthDomain(e.target.value)}
                />
                <Button variant="contained" size="small" style={{width: 'fit-content'}} color="primary" type="submit">Save Config</Button>
            </Stack>
            <Snackbar
                open={successDialog}
                onClose={handleCloseSuccessDialog}
                autoHideDuration={6000}
            >
                <Alert icon={<CheckIcon fontSize="inherit" />} severity="success">Config successfully saved.</Alert>
            </Snackbar>
        </form>        
    );
}

export default Configuration;