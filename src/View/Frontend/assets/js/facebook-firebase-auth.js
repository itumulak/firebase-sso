/**
 * Facebook Authentication.
 * Script for authenticating Facebook signup with Firebase SDK package.
 *
 * @since 2.0.0
 */

import { getAuth, signInWithPopup, FacebookAuthProvider } from "firebase/auth";

const facebookAuth = () => {
  const auth = getAuth();
  const provider = new FacebookAuthProvider();

  signInWithPopup(auth, provider)
    .then(async (result) => {
      const user = result.user;
      const credential = FacebookAuthProvider.credentialFromResult(result);
      const token = credential.accessToken;
      const formData = new FormData();
      formData.append("oauth_token", token);
      formData.append("refresh_token", user.refreshToken);
      formData.append("email", user.email);
      formData.append("action", firebase_sso_obect.action_facebook);
      formData.append("nonce", firebase_sso_obect.nonce);

      await fetch(firebase_sso_obect.ajaxurl, {
        method: "POST",
        body: formData,
        credentials: "same-origin",
      })
        .then((response) => {
          return response.json();
        })
        .then((response) => {
          if (response.success) {
            window.location.href = response.data.url;
          }
        });
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

document.getElementById('wp-firebase-facebook-sign-in').addEventListener('click', (event) => {
	facebookAuth();
});
