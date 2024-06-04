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
    const {wpLogin} = await import('./wp-auth.js');

    if (user) {
      wpLogin( user.uid, user.email, 'google', firebase_sso_object.action_login );
    } else {
      const provider = new GoogleAuthProvider();

      signInWithPopup(auth, provider)
        .then(async (result) => {
          const user = result.user;

          wpLogin( user.uid, user.email, 'google', firebase_sso_object.action_login);
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
