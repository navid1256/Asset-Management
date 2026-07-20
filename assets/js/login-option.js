"use strict";

const closeApplicationButton = document.querySelector("#close-application");

if (closeApplicationButton) {
    closeApplicationButton.addEventListener("click", () => {
        window.close();

        window.setTimeout(() => {
            if (!window.closed) {
                window.alert("مرورگر اجازه بستن این تب را نمی‌دهد. لطفاً تب را به‌صورت دستی ببندید.");
            }
        }, 150);
    });
}
