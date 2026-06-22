"use strict";

const assetButtons = document.querySelectorAll("[data-target-page]");

assetButtons.forEach((button) => {
    button.addEventListener("click", () => {
        const targetPage = button.dataset.targetPage;

        if (!targetPage) {
            return;
        }

        window.location.href = targetPage;
    });
});