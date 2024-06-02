import { initializeApp } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js";


document.addEventListener("DOMContentLoaded", () => {
  const providerButtons = document.querySelectorAll(".providers__list-btn");
  const firebaseConfig = sso_admin_object.config;
  
  initializeApp(firebaseConfig);

  providerButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
        event.preventDefault();

        const action = button.getAttribute('data-action');
        const provider = button.getAttribute('data-provider');

        if (action === 'connect') {
          console.log('hello');
        }
    });
  });
});
