import {
    getAuth,
    signOut,
    signInWithPopup,
    FacebookAuthProvider,
  } from "https://www.gstatic.com/firebasejs/10.7.1/firebase-auth.js";

  export const auth = () => {
    const auth = getAuth();
  
    auth.onAuthStateChanged((user) => {
      if (user) {
        signOut(auth)
          .then(() => {
            signIn(); 
          })
          .catch((error) => {}); 
      } else {
        signIn();
      }
    });
  };

  const signIn = async () => {
    const { linkCallback } = await import("./callback.js");
    const auth = getAuth();
    const provider = new FacebookAuthProvider();
  
    await signInWithPopup(auth, provider).then((result) => {
      const user = result.user;
  
      linkCallback(firebase_sso_object.user_id, user.uid, "facebook");
    });
  };
  