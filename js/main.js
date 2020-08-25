jQuery(document).ready((_) => {
    firebase.initializeApp(wp_firebase);

    _('#wp-firebase-google-sign-in').on('click', (event) => {
        const provider = new firebase.auth.GoogleAuthProvider();

        firebase.auth().signInWithPopup(provider)
            .then((result) => {
                const token = result.credential.accessToken;
                const user = result.user;

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

    _('#wp-firebase-facebook-sign-in').on('click', (event) => {
         const provider = new firebase.auth.FacebookAuthProvider();

        firebase.auth().signInWithPopup(provider)
                .then((result) => {
                    const token = result.credential.accessToken;
                    const user = result.user;

                    _.post(firebase_ajaxurl, {action: 'firebase_facebook_login', oauth_token: token, refresh_token: user.refreshToken, email: user.email }, (e, textStatus, jqXHR) => {
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

                    _.post(firebase_ajaxurl, {action: 'firebase_handle_error', code: error.code}, (e, textStatus, jqXHR) => {
                        if (e.success == true) {
                            if (_('#login_error')[0]) {
                                _('#login_error').text(e.data.message);

                                document.getElementById('loginform').classList.remove('shake');

                                setTimeout(() => {
                                    _('form#loginform').addClass('shake');

                                }, 1200);
                            }
                            else {
                                _(`<div id="login_error">${e.data.message}</div>`).insertBefore('form#loginform');
                                _('form#loginform').addClass('shake');
                            }
                        }
                    });
                })
    });

    if ( document.cookie.indexOf('wp_firebase_logout') !== -1 ) {
        firebase.auth().signOut()
            .then(() => {
                console.log('firebase signout.');
            });

    }
});