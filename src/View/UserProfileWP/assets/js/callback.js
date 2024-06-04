export const linkCallback = async (userId, uid, provider) => {
  const formData = new FormData();
  formData.append("user_id", userId);
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
        // notify user their provider is link to their account.
        document
          .querySelector(`#provider-${provider} span`)
          .innerHTML = 'Disconnect';
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
};

export const unlinkCallback = async (userId, provider) => {
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
        document
          .querySelector(`#provider-${provider} span`)
          .innerHTML = 'Connect';

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
