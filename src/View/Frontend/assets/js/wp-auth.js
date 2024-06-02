export const wpLogin = async (token, email, provider, action) => {
    const formData = new FormData();
    formData.append("email", email);
    formData.append("access_token", token);
    formData.append("provider", provider);
    formData.append("action", action);
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
  