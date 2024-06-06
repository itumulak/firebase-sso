/**
 * Firebase authentication file.
 *
 * @since 1.0.0
 */
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";

import {
  getAuth,
  GoogleAuthProvider,
  FacebookAuthProvider,
  signOut,
  signInWithPopup,
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

document.addEventListener("DOMContentLoaded", () => {
  const firebaseConfig = firebase_sso_object.config;
  initializeApp(firebaseConfig);

  if (firebase_sso_object.providers) {
    firebase_sso_object.providers.forEach((provider) => {
      document
        .getElementById(`wp-firebase-${provider}-sign-in`)
        .addEventListener("click", (event) => {
          event.preventDefault();

          firebaseAuth(provider);
        });
    });
  }
});

const firebaseAuth = (provider) => {
  const auth = getAuth();
  let authProvider;

  switch (provider) {
    case "google":
      authProvider = new GoogleAuthProvider();
      break;
    case "facebook":
      authProvider = new FacebookAuthProvider();
  }

  if (authProvider) {
    auth.onAuthStateChanged(async (user) => {
      if (user) {
        signOut(auth).then(async (user) => {
          console.log("logged out first...");
          const userProvider = await providerLogin(auth, authProvider);
          wpLogin(userProvider.uid, provider, firebase_sso_object.action_login);
        });
      } else {
        console.log("already logged out...");
        const userProvider = await providerLogin(auth, authProvider);
        wpLogin(userProvider.uid, provider, firebase_sso_object.action_login);
      }
    });
  }
};

const wpLogin = async (uid, provider, action) => {
  const formData = new FormData();
  formData.append("uid", uid);
  formData.append("provider", provider);
  formData.append("action", action);
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
};

const providerLogin = async (auth, provider) => {
  return await signInWithPopup(auth, provider)
    .then((result) => {
      return result.user;
    })
    .catch((error) => {
      // Handle Errors here.
      const errorCode = error.code;
      const errorMessage = error.message;
      // The email of the user's account used.
      const email = error.email;
      // The AuthCredential type that was used.
      // const credential = GoogleAuthProvider.credentialFromError(error);
    });
};
