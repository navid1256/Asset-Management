"use strict";

const USER_PROFILE_STORAGE_KEY = "itAssetUserProfile";

const getStoredUserProfile = () => {
    const storedProfile = localStorage.getItem(USER_PROFILE_STORAGE_KEY);

    if (!storedProfile) {
        return null;
    }

    try {
        return JSON.parse(storedProfile);
    } catch {
        localStorage.removeItem(USER_PROFILE_STORAGE_KEY);
        return null;
    }
};

const renderUserProfile = () => {
    const fullNameElement = document.querySelector("#profile-full-name");
    const departmentElement = document.querySelector("#profile-department");

    if (!fullNameElement || !departmentElement) {
        return;
    }

    const userProfile = getStoredUserProfile();

    if (!userProfile) {
        fullNameElement.textContent = "کاربر نامشخص";
        departmentElement.textContent = "معاونت / اداره نامشخص";
        return;
    }

    const fullName = `${userProfile.name} ${userProfile.familyName}`.trim();
    const department = `${userProfile.moavenat} / ${userProfile.edare}`;

    fullNameElement.textContent = fullName;
    departmentElement.textContent = department;
};

document.addEventListener("DOMContentLoaded", renderUserProfile);