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
    const loggedInUserElement = document.querySelector("#logged-in-username");

    if (!fullNameElement && !departmentElement && !loggedInUserElement) {
        return;
    }

    const userProfile = getStoredUserProfile();

    if (!userProfile) {
        if (fullNameElement) {
            fullNameElement.textContent = "کاربر نامشخص";
        }
        if (departmentElement) {
            departmentElement.textContent = "معاونت / اداره نامشخص";
        }
        if (loggedInUserElement) {
            loggedInUserElement.textContent = "کاربر نامشخص";
        }
        return;
    }

    const fullName = `${userProfile.name} ${userProfile.familyName}`.trim();
    const department = `${userProfile.moavenat} / ${userProfile.edare}`;
    const loggedInUser = userProfile.username || fullName || "کاربر نامشخص";

    if (fullNameElement) {
        fullNameElement.textContent = fullName;
    }
    if (departmentElement) {
        departmentElement.textContent = department;
    }
    if (loggedInUserElement) {
        loggedInUserElement.textContent = loggedInUser;
    }
};

document.addEventListener("DOMContentLoaded", renderUserProfile);
