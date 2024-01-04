/**
 * Base Authentication file.
 *
 * @since 1.0.0
 */

import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js';

document.addEventListener('DOMContentLoaded', () => {
	const firebaseConfig = firebase_sso_object.config;
	const app = initializeApp(firebaseConfig);

	if ( firebase_sso_object.providers ) {
		firebase_sso_object.providers.forEach((provider) => {	
			document.getElementById(`wp-firebase-${provider}-sign-in`).addEventListener('click', async (event) => {
				event.preventDefault();
				await import(`./${provider}-firebase-auth.js`)
					.then((module) => {
						module.auth();
					});
			});
		});
	}
});