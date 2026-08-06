"use strict";

const successMessage = document.querySelector("[data-success-redirect]");

// Redirect only after the server-confirmed success message is rendered.
if (successMessage) {
    const redirectUrl = successMessage.dataset.successRedirect;

    if (redirectUrl) {
        window.setTimeout(() => {
            window.location.assign(redirectUrl);
        }, 1000);
    }
}
