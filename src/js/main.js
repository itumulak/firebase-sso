/* eslint-disable no-undef */
import $ from 'jquery';

/**
 * Variables that are defined in wp_localized_script.
 *  - wp_firebase
 *  - firebase_ajaxurl
 */

$(document).ready((_) => {
	firebase.initializeApp(wp_firebase);

	_('#wp-firebase-google-sign-in').on('click', () => {
		const provider = new firebase.auth.GoogleAuthProvider();

		firebase
			.auth()
			.signInWithPopup(provider)
			.then((result) => {
				const token = result.credential.accessToken;
				const user = result.user;

				_.post(
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
				// The firebase.auth.AuthCredential type that was used.
				const credential = error.credential;
			});
	});

	_('#wp-firebase-facebook-sign-in').on('click', () => {
		const provider = new firebase.auth.FacebookAuthProvider();

		firebase
			.auth()
			.signInWithPopup(provider)
			.then((result) => {
				const token = result.credential.accessToken;
				const user = result.user;

				_.post(
					firebase_ajaxurl,
					{
						action: 'firebase_facebook_login',
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
				// The firebase.auth.AuthCredential type that was used.
				const credential = error.credential;

				_.post(
					firebase_ajaxurl,
					{ action: 'firebase_handle_error', code: error.code },
					(e) => {
						if (e.success === true) {
							if (_('#login_error')[0]) {
								_('#login_error').text(e.data.message);

								document
									.getElementById('loginform')
									.classList.remove('shake');

								setTimeout(() => {
									_('form#loginform').addClass('shake');
								}, 1200);
							} else {
								_(
									`<div id="login_error">${e.data.message}</div>`
								).insertBefore('form#loginform');
								_('form#loginform').addClass('shake');
							}
						}
					}
				);
			});
	});

	if (document.cookie.indexOf('wp_firebase_logout') !== -1) {
		firebase
			.auth()
			.signOut()
			.then(() => {
				console.log('firebase signout.');
			});
	}
});
