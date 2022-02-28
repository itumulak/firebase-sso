/**
 * Base Authentication file.
 *
 * @since 2.0.0
 */

import { initializeApp } from 'firebase/app';
import { getAuth, signOut } from 'firebase/auth';

import $ from 'jquery';
import googleAuth from './auth/google.auth';
import facebookAuth from './auth/facebook.auth';

let firebaseConfig;

$(document).ready(() => {
	$.post(
		// eslint-disable-next-line no-undef
		firebase_ajaxurl,
		{
			action: 'firebase_config',
		},
		(response) => {
			if (response.success === true) {
				// console.log(response.data.config);
				firebaseConfig = response.data.config;
				initializeApp(firebaseConfig);

				// @todo Implement a Firebase sign-out if they sign-out on WP...
				// const auth = getAuth();
				//
				// signOut(auth)
				// 	.then(() => {
				// 		// Sign-out successful.
				// 		console.log('Sign out...');
				// 	})
				// 	.catch((error) => {
				// 		// An error happened.
				// 	});
			}
		}
	);

	$('#wp-firebase-google-sign-in').on('click', () => {
		googleAuth();
	});

	$('#wp-firebase-facebook-sign-in').one('click', () => {
		facebookAuth();
	});
});
