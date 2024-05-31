export const wpLogin = async (credential, refreshToken, email, provider) => {
    const formData = new FormData();
    formData.append("credential", credential);
    formData.append("refresh_token", refreshToken);
    formData.append("email", email);
    formData.append("provider", provider);
    formData.append("action", firebase_sso_obect.action_login);
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
  
  export const wpRelogin = async (accessToken, email) => {
    const formData = new FormData();
    formData.append("access_token", accessToken);
    formData.append("email", email);
    formData.append("action", firebase_sso_object.action_relogin);
  
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
  