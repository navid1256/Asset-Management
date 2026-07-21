"use strict";

const passwordToggleIcons = `
    <svg data-password-icon="show" viewBox="0 0 24 24" aria-hidden="true">
        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
        <circle cx="12" cy="12" r="3"></circle>
    </svg>
    <svg data-password-icon="hide" viewBox="0 0 24 24" aria-hidden="true" hidden>
        <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
        <circle cx="12" cy="12" r="3"></circle>
        <path d="m3 3 18 18"></path>
    </svg>
`;

document.querySelectorAll("[data-password-toggle]").forEach((toggleButton) => {
    const passwordInput = document.getElementById(toggleButton.dataset.passwordToggle);

    if (!passwordInput) {
        return;
    }

    toggleButton.innerHTML = passwordToggleIcons;

    const showIcon = toggleButton.querySelector('[data-password-icon="show"]');
    const hideIcon = toggleButton.querySelector('[data-password-icon="hide"]');

    toggleButton.addEventListener("click", () => {
        const shouldShowPassword = passwordInput.type === "password";

        passwordInput.type = shouldShowPassword ? "text" : "password";
        passwordInput.dataset.passwordVisible = String(shouldShowPassword);
        toggleButton.setAttribute("aria-pressed", String(shouldShowPassword));
        toggleButton.setAttribute(
            "aria-label",
            shouldShowPassword ? "مخفی کردن رمز عبور" : "نمایش رمز عبور"
        );

        showIcon.toggleAttribute("hidden", shouldShowPassword);
        hideIcon.toggleAttribute("hidden", !shouldShowPassword);
        passwordInput.focus({ preventScroll: true });
    });
});
