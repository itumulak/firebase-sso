/**
 * Google Authentication.
 * Script for authenticating Google signup with Firebase SDK package.
 *
 * @since 2.0.0
 */

import {
  getAuth,
  signInWithPopup,
  GoogleAuthProvider,
} from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

export const auth = () => {
  const auth = getAuth();

  auth.onAuthStateChanged(async (user) => {
    if (user) {
      const formData = new FormData();
      formData.append("access_token", auth.currentUser.accessToken);
      formData.append("email", auth.currentUser.email);
      formData.append("action", firebase_sso_object.action_relogin);

      console.log( formData, user, firebase_sso_object );

      await fetch(firebase_sso_object.ajaxurl, {
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
    } else {
      const provider = new GoogleAuthProvider();

      signInWithPopup(auth, provider)
        .then(async (result) => {
          const credential = GoogleAuthProvider.credentialFromResult(result);
          const token = credential.accessToken;
          const user = result.user;
          const formData = new FormData();
          formData.append("oauth_token", token);
          formData.append("refresh_token", user.refreshToken);
          formData.append("email", user.email);
          formData.append("action", firebase_sso_obect.action_login);
          formData.append("provider", "google");
          formData.append("nonce", firebase_sso_object.nonce);

          await fetch(firebase_sso_object.ajaxurl, {
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
          const credential = GoogleAuthProvider.credentialFromError(error);
        });
    }
  });
};
