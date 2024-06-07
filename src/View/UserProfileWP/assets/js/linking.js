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

getRedirectResult(auth).then(async (result) => {
  if (result) {
    console.log("process redirect...");
    const uid = result.user.uid;
    const providerId = result.providerId;
    const provider = providerId.split(".");
    const formData = new FormData();
    formData.append("user_id", firebase_sso_object.user_id);
    formData.append("uid", uid);
    formData.append("provider", provider[0]);
    formData.append("action", firebase_sso_object.action);
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
        console.log(response);
        if (response.success) {
          const providerBtn = document.querySelector(
            `#provider-${provider} span`
          );
          providerBtn.innerHTML = "Disconnect";
          providerBtn.setAttribute("data-action", "disconnect");

          jQuery.toast({
            heading: "Success",
            text: "Successfully linked",
            showHideTransition: "slide",
            icon: "success",
            position: { top: 40, right: 80 },
          });
        } else {
          jQuery.toast({
            heading: "Error",
            text: "Already linked to an another account",
            showHideTransition: "slide",
            icon: "error",
            position: { top: 40, right: 80 },
          });
        }
      });
  }
});

document.addEventListener("DOMContentLoaded", () => {
  const providerButtons = document.querySelectorAll(".providers__list-btn");

  providerButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      event.preventDefault();

      const action = button.getAttribute("data-action");
      const provider = button.getAttribute("data-provider");

      if ("connect" === action) {
        linking(provider);
      } else if ("disconnect" === action) {
        unlink(firebase_sso_object.user_id, provider);
      }
    });
  });
});

const linking = (provider) => {
  let signInProvider;

  switch (provider) {
    case "google":
      signInProvider = new GoogleAuthProvider();
      break;
    case "facebook":
      signInProvider = new FacebookAuthProvider();
  }

  if (signInProvider) {
    auth.onAuthStateChanged(async (user) => {
      if (user) {
        signOut(auth).then(async (user) => {
          console.log("logged out first...");
          firebaseAuth(auth, signInProvider);
        });
      } else {
        console.log("already logged out...");
        firebaseAuth(auth, signInProvider);
      }
    });
  }
};

const unlink = async (userId, provider) => {
  const formData = new FormData();
  formData.append("user_id", userId);
  formData.append("provider", provider);
  formData.append("action", firebase_sso_object.unlink_action);
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
        const providerBtn = document.querySelector(
          `#provider-${provider} span`
        );
        providerBtn.innerHTML = "Connect";
        providerBtn.setAttribute("data-action", "connect");

        jQuery.toast({
          heading: "Success",
          text: "Successfully unlinked",
          showHideTransition: "slide",
          icon: "success",
          position: { top: 40, right: 80 },
        });
      }
    });
};

const firebaseAuth = async (auth, provider) => {
  return await signInWithRedirect(auth, provider)
    .then((result) => {
      return result.user;
    })
    .catch((error) => {
      const errorCode = error.code;
      const errorMessage = error.message;

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
