import Stack from "@mui/material/Stack"
import TextField from "@mui/material/TextField";
import Button from '@mui/material/Button';

const Configuration = () => {
    return (
        <Stack spacing={2}>
            <h1>Firebase Configurations</h1>
            <p>Get a copy, and paste your <a href="https://firebase.google.com/docs/web/setup?authuser=0#config-object">Firebase config object</a> found at your project settings.</p>
            <TextField 
                id="apiKey" 
                label="API Key" 
                variant="standard"
                type="password"
            />
            <TextField 
                id="authDomain" 
                label="Authorized Domain" 
                variant="standard"
                type="password"
            />
            <Button variant="contained" size="small" style={{width: 'fit-content'}} color="primary" type="submit">Save Config</Button>
        </Stack>
    );
}

export default Configuration;