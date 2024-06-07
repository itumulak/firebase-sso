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
  signInWithRedirect,
  getRedirectResult,
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

const firebaseConfig = firebase_sso_object.config;
initializeApp(firebaseConfig);

const auth = getAuth();

getRedirectResult(auth).then((result) => {
  if (result) {
    const user = result.user;
    const providerId = result.providerId;
    const provider = providerId.split("."); 
    wpLogin(user.uid, provider[0], firebase_sso_object.action_login);
  }
});

document.addEventListener("DOMContentLoaded", () => {
  if (firebase_sso_object.providers) {
    firebase_sso_object.providers.forEach((provider) => {
      const providerBtn = document.getElementById(
        `wp-firebase-${provider}-sign-in`
      );

      providerBtn.addEventListener("click", (event) => {
        event.preventDefault();

        firebaseAuth(provider);
      });
    });
  }
});

const firebaseAuth = (provider) => {
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
  return await signInWithRedirect(auth, provider)
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

      console.log(errorCode, errorMessage);

      jQuery.toast({
        heading: "Error",
        text: "An internal error occured. Please try again or contact plugin owner.",
        showHideTransition: "slide",
        icon: "error",
        position: { top: 40, right: 80 },
        duration: 8000,
      });
    });
};
