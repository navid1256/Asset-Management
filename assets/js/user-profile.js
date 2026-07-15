"use strict";

const USER_PROFILE_STORAGE_KEY = "itAssetUserProfile";
const CURRENT_USER_ENDPOINT = "../../process/current-user.php";

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

const renderReceiverProfile = () => {
    const fullNameElement = document.querySelector("#profile-full-name");
    const departmentElement = document.querySelector("#profile-department");

    if (!fullNameElement && !departmentElement) {
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
        return;
    }

    const fullName = `${userProfile.name} ${userProfile.familyName}`.trim();
    const department = `${userProfile.moavenat} / ${userProfile.edare}`;

    if (fullNameElement) {
        fullNameElement.textContent = fullName;
    }
    if (departmentElement) {
        departmentElement.textContent = department;
    }
};

const renderLoggedInUser = async () => {
    const loggedInUserElement =
        document.querySelector("#logged-in-username");

    if (!loggedInUserElement) {
        return;
    }

    try {
        const response = await fetch(CURRENT_USER_ENDPOINT, {
            headers: {
                Accept: "application/json",
            },
            cache: "no-store",
        });

        if (!response.ok) {
            switch (response.status) {
                case 400:
                    loggedInUserElement.textContent = "درخواست نامعتبر است";
                    break;

                case 401:
                    loggedInUserElement.textContent = "کاربر وارد نشده";
                    break;

                case 403:
                    loggedInUserElement.textContent = "دسترسی غیرمجاز است";
                    break;

                default:
                    if (response.status >= 500) {
                        loggedInUserElement.textContent =
                            "خطایی در سرور رخ داده است";
                    } else {
                        loggedInUserElement.textContent =
                            "دریافت اطلاعات کاربر ناموفق بود";
                    }
            }

            return;
        }

        const data = await response.json();

        loggedInUserElement.textContent =
            data.user?.fullName ||
            data.user?.username ||
            "نام کاربر مشخص نیست";
    } catch (error) {
        console.error("Failed to receive current user:", error);

        loggedInUserElement.textContent =
            "خطا در ارتباط با سرور";
    }
};

document.addEventListener("DOMContentLoaded", () => {
    renderReceiverProfile();
    renderLoggedInUser();
});
