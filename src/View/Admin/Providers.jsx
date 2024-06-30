import FormGroup from '@mui/material/FormGroup';
import FormControlLabel from '@mui/material/FormControlLabel';
import Switch from '@mui/material/Switch';
import Button from '@mui/material/Button';
import Stack from "@mui/material/Stack"

const Providers = () => {
    return (
        <Stack spacing={2}>
            <FormGroup>
                <FormControlLabel control={<Switch/>} label="Google" />
                <FormControlLabel control={<Switch/>} label="Facebook" />
            </FormGroup>
            <Button variant="contained" size="small" style={{width: 'fit-content'}} color="primary" type="submit">Save Providers</Button>
        </Stack>
    );
}

export default Providers;