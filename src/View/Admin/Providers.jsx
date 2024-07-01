import FormGroup from '@mui/material/FormGroup';
import FormControlLabel from '@mui/material/FormControlLabel';
import Switch from '@mui/material/Switch';
import Button from '@mui/material/Button';
import Stack from "@mui/material/Stack";
import Snackbar from '@mui/material/Snackbar';
import Alert from '@mui/material/Alert';
import CheckIcon from '@mui/icons-material/Check';
import { useState } from "react";

const Providers = () => {
    const [providers, setProviders] = useState(sso_object.providers);
    const [successDialog, setSuccessDialog] = useState(false);

    const handleToggle = (id) => {
        setProviders((prevState) => ({
            ...prevState,
            [id]: {
                ...prevState[id],
                is_active: !prevState[id].is_active
            }
        }))
    }

    const handleSubmit = async (event) => {
        event.preventDefault();

        const formData = new FormData();
        formData.append('action', sso_object.provider_action);
        formData.append('nonce', sso_object.nonce);

        Object.values(providers).map(item => {
            if (item.is_active) {
                formData.append('enabled_providers[]', item.id);
            }
        });

        await fetch(sso_object.ajaxurl, {
            method: "POST",
            body: formData,
            credentials: "same-origin",
        })
        .then((response) => {
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                handleOpenSucessDialog();
            }
            else {
                handleOpenSucessDialog();
            }
        });
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
                <FormGroup>
                    {Object.values(sso_object.providers).map(item => (                    
                        <FormControlLabel 
                            key={item.id} 
                            id={item.id} 
                            control={
                                <Switch 
                                    id={`${item.id}`} 
                                    defaultChecked={item.is_active} 
                                    onChange={() => handleToggle(item.id)} 
                                />
                                } label={item.label} 
                        />
                    ))}
                </FormGroup>
                <Button variant="contained" size="small" style={{width: 'fit-content'}} color="primary" type="submit">Save Providers</Button>
            </Stack>
            <Snackbar
                open={successDialog}
                onClose={handleCloseSuccessDialog}
                autoHideDuration={4000}
            >
                <Alert icon={<CheckIcon fontSize="inherit" />} severity="success">Providers successfully saved.</Alert>
            </Snackbar>
        </form>
        
    );
}

export default Providers;