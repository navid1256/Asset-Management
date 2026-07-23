"use strict";

(() => {
    const persianDigits = "۰۱۲۳۴۵۶۷۸۹";

    const toPersianDigits = (value) => String(value)
        .replace(/[0-9]/g, (digit) => persianDigits[Number(digit)]);

    const formatNumericInput = (input) => {
        const maxLength = input.maxLength > 0 ? input.maxLength : Infinity;

        input.value = toPersianDigits(input.value)
            .replace(/[^۰-۹]/g, "")
            .slice(0, maxLength);
    };

    document.addEventListener("input", (event) => {
        if (event.target.matches("input[data-persian-number]")) {
            formatNumericInput(event.target);
        }
    });

    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll("input[data-persian-number]").forEach(formatNumericInput);
    });

    window.PersianDigits = Object.freeze({
        toPersianDigits,
    });
})();
