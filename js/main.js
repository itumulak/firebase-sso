jQuery(document).ready((_) => {
    console.log(wp_firebase);

    firebase.initializeApp(wp_firebase);
    firebase.auth().signInWithEmailAndPassword('silverhythem@yahoo.com', 'ian052887!!')
        .then((response) => {
            if (response) {
                const $userInfo = {
                    uid: response.user.uid,
                    email: response.user.email,
                    displayName: response.user.displayName,
                    phoneNumber: response.user.phoneNumber,
                    photoUrl: response.user.photoUrl,
                    refreshToken: response.user.refreshToken,
                    tenantId: response.user.tenantId,
                    creationTime: response.user.metadata.creationTime,
                    lastSignInTime: response.user.metadata.lastSignInTime,
                    operationType: response.operationType,
                    providerId: response.additionalUserInfo.providerId
                }

                _.post(firebase_ajaxurl, {action: 'firebase_login', user: $userInfo}, (e, textStatus, jqXHR) => {
                    if (e.success == true) {

                    }
                });
            }
        })
        .catch((error) => {
            if ( ! error) {
                _.post(firebase_ajaxurl , {action: 'firebase_error', code: error.code, message: error.message}, (e, textStatus, jqXHR) => {
                    if (e.success != true) {
                        console.log(e.data.message);
                    }
                });
            }
        });
});