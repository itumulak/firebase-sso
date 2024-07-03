import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";

import {
  getAuth,
  GoogleAuthProvider,
  FacebookAuthProvider,
  signOut,
  signInWithPopup,
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

const firebaseConfig = firebase_sso_object.config;
initializeApp(firebaseConfig);

const auth = getAuth();

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

const linking = async (provider) => {
  let signInProvider;

  switch (provider) {
    case "google":
      signInProvider = new GoogleAuthProvider();
      break;
    case "facebook":
      signInProvider = new FacebookAuthProvider();
  }

  const requestedLinked = await signInWithPopup(auth, signInProvider)
  .then((result) => {
    return result.user;
  })
  .catch((error) => {
    const errorCode = error.code;
    const errorMessage = error.message;

    console.log(errorCode, errorMessage, 'hey3');

    jQuery.toast({
      heading: "Error",
      text: "An internal error occured. Please try again or contact plugin owner.",
      showHideTransition: "slide",
      icon: "error",
      position: { top: 40, right: 80 },
      duration: 8000,
    });
  });

  if (requestedLinked.accessToken) {
    const uid = requestedLinked.uid;
    const formData = new FormData();
    
    formData.append("user_id", firebase_sso_object.user_id);
    formData.append("uid", uid);
    formData.append("provider", provider);
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
  console.log(auth, provider, 'hey1');

  return await signInWithPopup(auth, provider)
    .then((result) => {
      console.log(result, 'hey2');
      return result.user;
    })
    .catch((error) => {
      const errorCode = error.code;
      const errorMessage = error.message;

      console.log(errorCode, errorMessage, 'hey3');

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
