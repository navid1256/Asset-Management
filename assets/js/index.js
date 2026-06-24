"use strict";

const USER_PROFILE_STORAGE_KEY = "itAssetUserProfile";

const userInfoForm = document.querySelector("#user-info-form");
const nameInputs = document.querySelectorAll("#name, #family-name");
const digitPattern = /[0-9۰-۹٠-٩]/;
const allDigitsPattern = /[0-9۰-۹٠-٩]/g;

const getInputValue = (selector) => {
    const input = document.querySelector(selector);
    return input ? input.value.trim() : "";
};

const getSelectedText = (selector) => {
    const select = document.querySelector(selector);

    if (!select || !select.value) {
        return "";
    }

    return select.options[select.selectedIndex].textContent.trim();
};

const removeDigits = (value) => value.replace(allDigitsPattern, "");

nameInputs.forEach((input) => {
    input.addEventListener("beforeinput", (event) => {
        if (event.data && digitPattern.test(event.data)) {
            event.preventDefault();
        }
    });

    input.addEventListener("input", () => {
        const valueWithoutDigits = removeDigits(input.value);
        if (input.value !== valueWithoutDigits) {
            input.value = valueWithoutDigits;
        }
    });
});

const saveUserProfile = () => {
    const userProfile = {
        name: getInputValue("#name"),
        familyName: getInputValue("#family-name"),
        nationalCode: getInputValue("#national-code"),
        moavenat: getSelectedText("#moavenat"),
        edare: getSelectedText("#edare")
    };

    if (!userProfile.name || !userProfile.familyName || !userProfile.moavenat || !userProfile.edare) {
        alert("لطفاً نام، نام خانوادگی، معاونت و اداره را کامل وارد کنید.");
        return false;
    }

    if (digitPattern.test(userProfile.name) || digitPattern.test(userProfile.familyName)) {
        alert("نام و نام خانوادگی نباید شامل عدد باشد.");
        return false;
    }

    localStorage.setItem(USER_PROFILE_STORAGE_KEY, JSON.stringify(userProfile));

    return true;
};

userInfoForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const isSaved = saveUserProfile();

    if (isSaved) {
        window.location.href = "assets.html";
    }
});
