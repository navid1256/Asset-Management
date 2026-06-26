"use strict";

const CASE_STATUS_STORAGE_KEY = "itAssetCaseStatus";

const newCaseSection = document.querySelector(".new-case");
const oldCaseSection = document.querySelector(".old-case");

const getStoredCaseStatus = () => {
    const storedStatus = localStorage.getItem(CASE_STATUS_STORAGE_KEY);

    if (!storedStatus) {
        return null;
    }

    try {
        return JSON.parse(storedStatus);
    } catch {
        localStorage.removeItem(CASE_STATUS_STORAGE_KEY);
        return null;
    }
};

const setSectionState = (section, isEnabled) => {
    if (!section) {
        return;
    }

    section.classList.toggle("is-disabled", !isEnabled);
    section.setAttribute("aria-disabled", String(!isEnabled));

    section.querySelectorAll("input, select, textarea, button").forEach((field) => {
        field.disabled = !isEnabled;
    });
};

const renderCaseStatus = () => {
    const caseStatus = getStoredCaseStatus();

    if (!caseStatus || !caseStatus.caseType) {
        setSectionState(newCaseSection, true);
        setSectionState(oldCaseSection, true);
        return;
    }

    const isNewCase = caseStatus.caseType === "new";

    setSectionState(newCaseSection, isNewCase);
    setSectionState(oldCaseSection, !isNewCase);
};

document.addEventListener("DOMContentLoaded", renderCaseStatus);
