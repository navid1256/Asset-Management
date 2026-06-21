const slotNumberSelect = document.getElementById('slot-number');
const ramSlots = document.getElementById('ram-slots');
const ramRowTemplate = ramSlots.querySelector('[data-ram-row]').cloneNode(true);

const hardNumberSelect = document.getElementById('hard-number');
const storageNumber = document.getElementById('storage-number');
const hardRowTemplate = storageNumber.querySelector('[data-hard-row]').cloneNode(true);

const dvdWriterEnabled = document.getElementById('dvd-writer-enabled');
const dvdWriterSection = document.querySelector('.dvd-writer-section');

const deliverySheetInput = document.getElementById('delivery-sheet');
const deliveryFileName = document.getElementById('delivery-file-name');

const totalRamCapacity = document.getElementById('total-ram-capacity');
const totalStorageCapacity = document.getElementById('total-storage-capacity');

function toggleDVDWriterSection() {
    const isEnabled = dvdWriterEnabled.checked;

    dvdWriterSection.classList.toggle('is-disabled', !isEnabled);

    dvdWriterSection.querySelectorAll('select, input').forEach((field) => {
        field.disabled = !isEnabled;
    });
}

function updateRamFieldIds(row, rowNumber) {
    row.querySelectorAll('label[for]').forEach((label) => {
        const baseFor = label.htmlFor.replace(/-\d+$/, '');
        label.htmlFor = `${baseFor}-${rowNumber}`;
    });

    row.querySelectorAll('select, input').forEach((field) => {
        const baseId = field.id.replace(/-\d+$/, '');
        field.id = `${baseId}-${rowNumber}`;
    });
}

function updateHardFieldIds(row, rowNumber) {
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

    const ramCapacitySelects = ramSlots.querySelectorAll('select[name="ram-capacity[]"]');

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

    const hardCapacitySelects = storageNumber.querySelectorAll('select[name="hard-capacity[]"]');

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
        updateRamFieldIds(row, index + 1);
    });

    updateTotalRamCapacity();
}

function renderHardRows() {
    const rowCount = Number(hardNumberSelect.value) || 1;

    storageNumber.innerHTML = '';

    for (let index = 1; index <= rowCount; index += 1) {
        const row = hardRowTemplate.cloneNode(true);

        updateHardFieldIds(row, index);

        if (index > 1) {
            resetRowFields(row);
        }

        storageNumber.appendChild(row);
    }

    updateTotalStorageCapacity();
}

deliverySheetInput.addEventListener('change', () => {
    const selectedFile = deliverySheetInput.files[0];

    deliveryFileName.textContent = selectedFile
        ? selectedFile.name
        : 'فایلی انتخاب نشده';

    deliveryFileName.title = selectedFile ? selectedFile.name : '';
});

slotNumberSelect.addEventListener('change', renderRamRows);
hardNumberSelect.addEventListener('change', renderHardRows);

ramSlots.addEventListener('change', updateTotalRamCapacity);
storageNumber.addEventListener('change', updateTotalStorageCapacity);

dvdWriterEnabled.addEventListener('change', toggleDVDWriterSection);

toggleDVDWriterSection();
renderRamRows();
renderHardRows();
updateTotalRamCapacity();
updateTotalStorageCapacity();