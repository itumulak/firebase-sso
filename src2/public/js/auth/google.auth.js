/**
 * Google Authentication.
 * Script for authenticating Google signup with Firebase SDK package.
 *
 * @since 2.0.0
 */

import { getAuth, signInWithPopup, GoogleAuthProvider } from 'firebase/auth';
import $ from 'jquery';

const googleAuth = () => {
	const auth = getAuth();
	const provider = new GoogleAuthProvider();

	signInWithPopup(auth, provider)
		.then((result) => {
			const credential = GoogleAuthProvider.credentialFromResult(result);
			const token = credential.accessToken;
			const user = result.user;

			$.post(
				// eslint-disable-next-line no-undef
				firebase_ajaxurl,
				{
					action: 'firebase_google_login',
					oauth_token: token,
					refresh_token: user.refreshToken,
					email: user.email,
					security: sso_firebase_nonce,
				},
				(e) => {
					if (e.success === true) {
						window.location.href = e.data.url;
					}
				}
			);
		})
		.catch((error) => {
			// Handle Errors here.
			const errorCode = error.code;
			const errorMessage = error.message;
			// The email of the user's account used.
			const email = error.email;
			// The AuthCredential type that was used.
			const credential = GoogleAuthProvider.credentialFromError(error);
		});
};

export default googleAuth;
