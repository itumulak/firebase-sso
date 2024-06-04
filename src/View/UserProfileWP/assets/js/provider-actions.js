import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";


document.addEventListener("DOMContentLoaded", () => {
  const providerButtons = document.querySelectorAll(".providers__list-btn");
  const firebaseConfig = firebase_sso_object.config;
  
  initializeApp(firebaseConfig);

  providerButtons.forEach((button) => {
    button.addEventListener("click", async (event) => {
        event.preventDefault();

        const action = button.getAttribute('data-action');
        const provider = button.getAttribute('data-provider');

        if (action === 'connect') {
          await import(`./${provider}-provider.js`).then((module) => {
            module.auth();  
          });
        }
        else if ('disconnect' === action) {
          const {unlinkCallback} = await import('./callback.js');

          unlinkCallback(firebase_sso_object.user_id, provider);
        }
    });
  });
});
