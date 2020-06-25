jQuery(document).ready((_) => {
    firebase.initializeApp(wp_firebase);

    _('#wp-firebase-google-sign-in').on('click', (event) => {
        const provider = new firebase.auth.GoogleAuthProvider();

        firebase.auth().signInWithPopup(provider)
            .then((result) => {
                const token = result.credential.accessToken;
                const user = result.user;

//                console.log(token, user);
                _.post(firebase_ajaxurl, {action: 'firebase_google_login', oauth_token: token, refresh_token: user.refreshToken, email: user.email }, (e, textStatus, jqXHR) => {
                    if (e.success == true) {
                        window.location.href = e.data.url;
                    }
                });
            })
            .catch((error) => {
                // Handle Errors here.
                var errorCode = error.code;
                var errorMessage = error.message;
                // The email of the user's account used.
                var email = error.email;
                // The firebase.auth.AuthCredential type that was used.
                var credential = error.credential;
            })
    });

    const cookie = document.cookie.indexOf('wp_firebase') == -1;

    console.log();

    if ( document.cookie.indexOf('wp_firebase_logout') !== -1 ) {
        firebase.auth().signOut()
            .then(() => {
                console.log('firebase signout.');
            });

    }

//    console.log(wp_firebase);

//    firebase.initializeApp(wp_firebase);
//    firebase.auth().signInWithEmailAndPassword('silverhythem@yahoo.com', 'ian052887!!')
//        .then((response) => {
//            if (response) {
//                const $userInfo = {
//                    uid: response.user.uid,
//                    email: response.user.email,
//                    displayName: response.user.displayName,
//                    phoneNumber: response.user.phoneNumber,
//                    photoUrl: response.user.photoUrl,
//                    refreshToken: response.user.refreshToken,
//                    tenantId: response.user.tenantId,
//                    creationTime: response.user.metadata.creationTime,
//                    lastSignInTime: response.user.metadata.lastSignInTime,
//                    operationType: response.operationType,
//                    providerId: response.additionalUserInfo.providerId
//                }
//
//                _.post(firebase_ajaxurl, {action: 'firebase_login', user: $userInfo}, (e, textStatus, jqXHR) => {
//                    if (e.success == true) {
//
//                    }
//                });
//            }
//        })
//        .catch((error) => {
//            if ( ! error) {
//                _.post(firebase_ajaxurl , {action: 'firebase_error', code: error.code, message: error.message}, (e, textStatus, jqXHR) => {
//                    if (e.success != true) {
//                        console.log(e.data.message);
//                    }
//                });
//            }
//        });
});