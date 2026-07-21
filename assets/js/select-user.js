"use strict";

const USER_PROFILE_STORAGE_KEY = "itAssetUserProfile";

const userInfoForm = document.querySelector("#user-info-form");
const nameInputs = document.querySelectorAll("#name, #family-name");
const nationalCodeInput = document.querySelector("#national-code");
const digitPattern = /[0-9۰-۹٠-٩]/;
const allDigitsPattern = /[0-9۰-۹٠-٩]/g;
const nonDigitPattern = /[^0-9۰-۹٠-٩]/;
const allNonDigitsPattern = /[^0-9۰-۹٠-٩]/g;
const nationalCodePattern = /^[0-9۰-۹٠-٩]{10}$/;

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
const sanitizeNationalCode = (value) => value.replace(allNonDigitsPattern, "").slice(0, 10);

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

if (nationalCodeInput) {
    nationalCodeInput.addEventListener("beforeinput", (event) => {
        if (!event.data) {
            return;
        }

        if (nonDigitPattern.test(event.data)) {
            event.preventDefault();
            return;
        }

        const selectionLength = nationalCodeInput.selectionEnd - nationalCodeInput.selectionStart;
        const nextLength = nationalCodeInput.value.length - selectionLength + event.data.length;

        if (nextLength > 10) {
            event.preventDefault();
        }
    });

    nationalCodeInput.addEventListener("input", () => {
        const sanitizedValue = sanitizeNationalCode(nationalCodeInput.value);
        if (nationalCodeInput.value !== sanitizedValue) {
            nationalCodeInput.value = sanitizedValue;
        }
    });
}

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

    if (!nationalCodePattern.test(userProfile.nationalCode)) {
        alert("کد ملی باید دقیقاً ۱۰ رقم باشد.");
        return false;
    }

    localStorage.setItem(USER_PROFILE_STORAGE_KEY, JSON.stringify(userProfile));

    return true;
};

userInfoForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const isSaved = saveUserProfile();

    if (isSaved) {
        window.location.href = userInfoForm.action;
    }
});
