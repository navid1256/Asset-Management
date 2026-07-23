"use strict";

(() => {
const USER_PROFILE_STORAGE_KEY = "itAssetUserProfile";

const userInfoForm = document.querySelector("#user-info-form");
const nameInputs = document.querySelectorAll("#name, #family-name");
const deputySelect = document.querySelector("#moavenat");
const departmentSelect = document.querySelector("#edare");

const digitPattern = /\p{N}/u;
const allDigitsPattern = /\p{N}/gu;
const nationalCodePattern = /^[۰-۹]{10}$/;
const mobilePattern = /^۰۹[۰-۹]{9}$/;

const getInputValue = (selector) => {
    const input = document.querySelector(selector);
    return input ? input.value.trim() : "";
};

const getSelectedText = (select) => {
    if (!select.value) {
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
        input.value = removeDigits(input.value);
    });
});

const filterDepartments = () => {
    const selectedDeputyId = deputySelect.value;
    let hasSelectedDepartment = false;

    Array.from(departmentSelect.options).forEach((option, index) => {
        if (index === 0) {
            return;
        }

        const belongsToSelectedDeputy =
            option.dataset.deputyId === selectedDeputyId;

        option.hidden = !belongsToSelectedDeputy;
        option.disabled = !belongsToSelectedDeputy;

        if (option.selected && belongsToSelectedDeputy) {
            hasSelectedDepartment = true;
        }
    });

    departmentSelect.disabled = selectedDeputyId === "";

    if (!hasSelectedDepartment) {
        departmentSelect.value = "";
    }
};

const validateUserInfo = () => {
    const firstName = getInputValue("#name");
    const lastName = getInputValue("#family-name");
    const nationalCode = getInputValue("#national-code");
    const mobile = getInputValue("#mobile");

    if (!firstName || !lastName || !deputySelect.value || !departmentSelect.value) {
        alert("لطفاً تمام اطلاعات تحویل‌گیرنده را کامل وارد کنید.");
        return false;
    }

    if (digitPattern.test(firstName) || digitPattern.test(lastName)) {
        alert("نام و نام خانوادگی نباید شامل عدد باشد.");
        return false;
    }

    if (!nationalCodePattern.test(nationalCode)) {
        alert("کد ملی باید دقیقاً ۱۰ رقم باشد.");
        return false;
    }

    if (!mobilePattern.test(mobile)) {
        alert("شماره همراه باید ۱۱ رقم و با ۰۹ شروع شود.");
        return false;
    }

    return true;
};

const saveUserProfile = () => {
    const userProfile = {
        name: getInputValue("#name"),
        familyName: getInputValue("#family-name"),
        nationalCode: getInputValue("#national-code"),
        mobile: getInputValue("#mobile"),
        moavenat: getSelectedText(deputySelect),
        edare: getSelectedText(departmentSelect),
    };

    localStorage.setItem(
        USER_PROFILE_STORAGE_KEY,
        JSON.stringify(userProfile)
    );
};

deputySelect.addEventListener("change", filterDepartments);

userInfoForm.addEventListener("submit", (event) => {
    if (!validateUserInfo()) {
        event.preventDefault();
        return;
    }

    saveUserProfile();
});

filterDepartments();
})();
