import { getAuth, signInWithPopup, FacebookAuthProvider } from 'firebase/auth';
import $ from 'jquery';

const facebookAuth = () => {
	const auth = getAuth();
	const provider = new FacebookAuthProvider();

	signInWithPopup(auth, provider)
		.then((result) => {
			const user = result.user;
			const credential =
				FacebookAuthProvider.credentialFromResult(result);
			const accessToken = credential.accessToken;

			$.post(
				// eslint-disable-next-line no-undef
				firebase_ajaxurl,
				{
					action: 'firebase_facebook_login',
					oauth_token: accessToken,
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
			const credential = FacebookAuthProvider.credentialFromError(error);
		});
};

export default facebookAuth;
