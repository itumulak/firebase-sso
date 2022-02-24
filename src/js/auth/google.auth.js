import { getAuth, signInWithPopup, GoogleAuthProvider } from 'firebase/auth';
import $ from 'jquery';
import { initializeApp } from 'firebase/app';

const auth = getAuth();
const provider = new GoogleAuthProvider();

const googleAuth = () => {
	$.post(
		// eslint-disable-next-line no-undef
		firebase_ajaxurl,
		{
			action: 'firebase_config',
		},
		(response) => {
			if (response.success === true) {
				initializeApp(response.data.config);

				signInWithPopup(auth, provider)
					.then((result) => {
						const token = result.credential.accessToken;
						const user = result.user;

						$.post(
							// eslint-disable-next-line no-undef
							firebase_ajaxurl,
							{
								action: 'firebase_google_login',
								oauth_token: token,
								refresh_token: user.refreshToken,
								email: user.email,
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
						// ...
					});
			}
		}
	);
};

export default googleAuth;
