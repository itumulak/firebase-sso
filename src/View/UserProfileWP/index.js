import Providers from "./Providers.jsx";
import { createRoot } from "react-dom/client";

const root = createRoot(document.getElementById('active-providers'));
root.render(<Providers/>);