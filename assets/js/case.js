const slotNumberSelect = document.getElementById('slot-number');
const ramSlots = document.getElementById('ram-slots');
const ramRowTemplate = ramSlots.querySelector('[data-ram-row]').cloneNode(true);

const hardNumberSelect = document.getElementById('hard-number');
const storageNumber = document.getElementById('storage-number');
const hardRowTemplate = storageNumber.querySelector('[data-hard-row]').cloneNode(true);

const dvdWriterEnabled = document.getElementById('dvd-writer-enabled');
const dvdWriterSection = document.querySelector('.dvd-writer-section');

const gpuOnboardRadio = document.getElementById('gpu-onboard');
const gpuInternalRadio = document.getElementById('gpu-internal');
const gpuContainer = document.querySelector('.gpu');

const gpuFields = document.querySelectorAll(
    '#gpu-brand, #gpu-model, #gpu-memory'
);

const gpuUploadInput = document.getElementById('gpu-upload');
const gpuUploadButton = document.querySelector('label[for="gpu-upload"]');

const writerUploadInput = document.getElementById('writer-upload');
const writerUploadButton = document.querySelector('label[for="writer-upload"]');

const totalRamCapacity = document.getElementById('total-ram-capacity');
const totalStorageCapacity = document.getElementById('total-storage-capacity');

const fileUploadFields = [
    {
        inputId: 'delivery-sheet',
        fileNameSelector: '#delivery-file-name',
    },
    {
        inputId: 'cpu-upload',
        fileNameSelector: '[data-file-name-for="cpu-upload"]',
    },
    {
        inputId: 'motherboard-upload',
        fileNameSelector: '[data-file-name-for="motherboard-upload"]',
    },
    {
        inputId: 'gpu-upload',
        fileNameSelector: '[data-file-name-for="gpu-upload"]',
    },
    {
        inputId: 'ram-upload',
        fileNameSelector: '[data-file-name-for="ram-upload"]',
    },
    {
        inputId: 'writer-upload',
        fileNameSelector: '[data-file-name-for="writer-upload"]',
    },
    {
        inputId: 'power-upload',
        fileNameSelector: '[data-file-name-for="power-upload"]',
    },
    {
        inputId: 'case-upload',
        fileNameSelector: '[data-file-name-for="case-upload"]',
    },
];

function limitNumericInput(inputId, maxLength) {
    const input = document.getElementById(inputId);

    if (!input) {
        return;
    }

    input.addEventListener('input', () => {
        input.value = input.value
            .replace(/[^0-9۰-۹٠-٩]/g, '')
            .slice(0, maxLength);
    });
}

function setWriterUploadState(isEnabled) {
    if (!writerUploadInput || !writerUploadButton) {
        return;
    }

    writerUploadInput.disabled = !isEnabled;
    writerUploadButton.classList.toggle('is-disabled', !isEnabled);
    writerUploadButton.setAttribute('aria-disabled', String(!isEnabled));

    if (!isEnabled) {
        resetFileUpload('writer-upload');
    }
}

function toggleDVDWriterSection() {
    const isEnabled = dvdWriterEnabled.checked;

    dvdWriterSection.classList.toggle('is-disabled', !isEnabled);

    dvdWriterSection.querySelectorAll('select, input').forEach((field) => {
        field.disabled = !isEnabled;
    });

    setWriterUploadState(isEnabled);
}

function setGpuUploadState(isEnabled) {
    if (!gpuUploadInput || !gpuUploadButton) {
        return;
    }

    gpuUploadInput.disabled = !isEnabled;
    gpuUploadButton.classList.toggle('is-disabled', !isEnabled);
    gpuUploadButton.setAttribute('aria-disabled', String(!isEnabled));

    if (!isEnabled) {
        resetFileUpload('gpu-upload');
    }
}

function toggleGpuSection() {
    if (!gpuOnboardRadio || !gpuInternalRadio) {
        return;
    }

    const isInternalGpu = gpuInternalRadio.checked;

    gpuFields.forEach((field) => {
        field.disabled = !isInternalGpu;
    });

    if (gpuContainer) {
        gpuContainer.classList.toggle('is-onboard', !isInternalGpu);
    }

    setGpuUploadState(isInternalGpu);
}
function updateRowFieldIds(row, rowNumber) {
    row.querySelectorAll('label[for]').forEach((label) => {
        const baseFor = label.htmlFor.replace(/-\d+$/, '');
        label.htmlFor = `${baseFor}-${rowNumber}`;
    });

    row.querySelectorAll('select, input').forEach((field) => {
        const baseId = field.id.replace(/-\d+$/, '');
        field.id = `${baseId}-${rowNumber}`;
    });
}

function resetRowFields(row) {
    row.querySelectorAll('select').forEach((select) => {
        select.selectedIndex = 0;
    });

    row.querySelectorAll('input').forEach((input) => {
        if (input.type === 'radio' || input.type === 'checkbox') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
}

function formatStorageCapacity(totalGB) {
    if (totalGB >= 1024 && totalGB % 1024 === 0) {
        return `${totalGB / 1024}TB`;
    }

    if (totalGB >= 1024) {
        return `${(totalGB / 1024).toFixed(2)}TB`;
    }

    return `${totalGB}GB`;
}

function updateTotalRamCapacity() {
    if (!totalRamCapacity) {
        return;
    }

    const ramCapacitySelects = ramSlots.querySelectorAll('select[name="ram_capacity_gb[]"]');

    let totalGB = 0;

    ramCapacitySelects.forEach((select) => {
        totalGB += Number(select.value) || 0;
    });

    totalRamCapacity.textContent = `${totalGB}GB`;
}

function updateTotalStorageCapacity() {
    if (!totalStorageCapacity) {
        return;
    }

    const hardCapacitySelects = storageNumber.querySelectorAll('select[name="storage_capacity_gb[]"]');

    let totalGB = 0;

    hardCapacitySelects.forEach((select) => {
        totalGB += Number(select.value) || 0;
    });

    totalStorageCapacity.textContent = formatStorageCapacity(totalGB);
}

function renderRamRows() {
    const rowCount = Number(slotNumberSelect.value) || 1;
    const rows = Array.from(ramSlots.querySelectorAll('[data-ram-row]'));

    while (rows.length > rowCount) {
        rows.pop().remove();
    }

    while (rows.length < rowCount) {
        const row = ramRowTemplate.cloneNode(true);

        resetRowFields(row);

        rows.push(row);
        ramSlots.appendChild(row);
    }

    rows.forEach((row, index) => {
        updateRowFieldIds(row, index + 1);
    });

    updateTotalRamCapacity();
}

function renderHardRows() {
    const rowCount = Number(hardNumberSelect.value) || 1;

    storageNumber.innerHTML = '';

    for (let index = 1; index <= rowCount; index += 1) {
        const row = hardRowTemplate.cloneNode(true);

        updateRowFieldIds(row, index);

        if (index > 1) {
            resetRowFields(row);
        }

        storageNumber.appendChild(row);
    }

    updateTotalStorageCapacity();
}

function getSelectedFileName(inputElement) {
    return inputElement.files[0] ? inputElement.files[0].name : 'فایلی انتخاب نشده';
}

function updateFileName(inputElement, fileNameElement) {
    if (!inputElement || !fileNameElement) {
        return;
    }

    const fileName = getSelectedFileName(inputElement);

    fileNameElement.textContent = fileName;
    fileNameElement.title = inputElement.files[0] ? fileName : '';
}

function updateStorageWarrantyFileName(event) {
    const inputElement = event.target;

    if (!inputElement.matches('.storage-warranty-input')) {
        return;
    }

    const uploadContainer = inputElement.closest('.hard-warranty');
    const fileNameElement = uploadContainer?.querySelector('[data-storage-file-name]');

    updateFileName(inputElement, fileNameElement);
}

function bindFileUpload({ inputId, fileNameSelector }) {
    const inputElement = document.getElementById(inputId);
    const fileNameElement = document.querySelector(fileNameSelector);

    if (!inputElement || !fileNameElement) {
        return;
    }

    inputElement.addEventListener('change', () => {
        updateFileName(inputElement, fileNameElement);
    });
}

function bindFileUploads() {
    fileUploadFields.forEach(bindFileUpload);
}

function resetFileUpload(inputId) {
    const uploadField = fileUploadFields.find((field) => field.inputId === inputId);

    if (!uploadField) {
        return;
    }

    const inputElement = document.getElementById(uploadField.inputId);
    const fileNameElement = document.querySelector(uploadField.fileNameSelector);

    if (!inputElement || !fileNameElement) {
        return;
    }

    inputElement.value = '';
    updateFileName(inputElement, fileNameElement);
}

slotNumberSelect.addEventListener('change', renderRamRows);
hardNumberSelect.addEventListener('change', renderHardRows);

ramSlots.addEventListener('change', updateTotalRamCapacity);
storageNumber.addEventListener('change', updateTotalStorageCapacity);
storageNumber.addEventListener('change', updateStorageWarrantyFileName);

dvdWriterEnabled.addEventListener('change', toggleDVDWriterSection);

gpuOnboardRadio.addEventListener('change', toggleGpuSection);
gpuInternalRadio.addEventListener('change', toggleGpuSection);

limitNumericInput('asset-number', 5);
limitNumericInput('it-number', 4);

bindFileUploads();

toggleDVDWriterSection();
toggleGpuSection();

renderRamRows();
renderHardRows();
updateTotalRamCapacity();
updateTotalStorageCapacity();
