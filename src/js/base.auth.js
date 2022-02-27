import { initializeApp } from 'firebase/app';
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
			}
		}
	);

	$('#wp-firebase-googlae-sign-in').on('click', () => {
		googleAuth();
	});

	$('#wp-firebase-facebook-sign-in').one('click', () => {
		facebookAuth();
	});
});
