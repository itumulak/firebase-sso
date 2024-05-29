/**
 * Facebook Authentication.
 * Script for authenticating Facebook signup with Firebase SDK package.
 *
 * @since 2.0.0
 */

import {
  getAuth,
  signInWithPopup,
  FacebookAuthProvider,
} from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

export const auth = () => {
  const auth = getAuth();

  auth.onAuthStateChanged(async (user) => {
    const { wpLogin, wpRelogin } = await import("./wp-auth.js");

    if (user) {
      wpRelogin(user.accessToken, user.email);
    } else {
      const provider = new FacebookAuthProvider();

      signInWithPopup(auth, provider)
        .then(async (result) => {
          const user = result.user;
          const credential = FacebookAuthProvider.credentialFromResult(result);
          const token = credential.accessToken;

          wpLogin(token, user.refreshToken, user.email, "facebook");
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
    }
  });
};
