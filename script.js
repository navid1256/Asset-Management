const slotNumberSelect = document.getElementById('slot-number');
const ramSlots = document.getElementById('ram-slots');
const ramRowTemplate = ramSlots.querySelector('[data-ram-row]').cloneNode(true);
const hardNumberSelect = document.getElementById('hard-number');
const storageNumber = document.getElementById('storage-number');
const hardRowTemplate = storageNumber.querySelector('[data-hard-row]').cloneNode(true);
const dvdWriterNumberSelect = document.getElementById('dvd-writer-number');
const dvdWriterNumber = document.getElementById('dvd-writer-numbers');
const dvdWriterRowTemplate = dvdWriterNumber.querySelector('[data-dvd-writer-row]').cloneNode(true);
const dvdWriterEnabled = document.getElementById('dvd-writer-enabled');
const dvdWriterSection = document.querySelector('.dvd-writer-section');

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

function updateDVDWriterFieldIds(row, rowNumber) {
    row.querySelectorAll('label[for]').forEach((label) => {
        const baseFor = label.htmlFor.replace(/-\d+$/, '');
        label.htmlFor = `${baseFor}-${rowNumber}`;
    });

    row.querySelectorAll('select, input').forEach((field) => {
        const baseId = field.id.replace(/-\d+$/, '');
        field.id = `${baseId}-${rowNumber}`;
    });
}

function renderRamRows() {
    const rowCount = Number(slotNumberSelect.value) || 1;
    const rows = Array.from(ramSlots.querySelectorAll('[data-ram-row]'));

    while (rows.length > rowCount) {
        rows.pop().remove();
    }

    while (rows.length < rowCount) {
        const previousRow = rows[rows.length - 1] || ramRowTemplate;
        const row = previousRow.cloneNode(true);
        rows.push(row);
        ramSlots.appendChild(row);
    }

    rows.forEach((row, index) => {
        updateRamFieldIds(row, index + 1);
    });
}

function renderHardRows() {
    const rowCount = Number(hardNumberSelect.value) || 1;

    storageNumber.innerHTML = '';

    for (let index = 1; index <= rowCount; index += 1) {
        const row = hardRowTemplate.cloneNode(true);
        updateHardFieldIds(row, index);

        if (index > 1) {
            row.querySelectorAll('select').forEach((select) => {
                select.selectedIndex = 0;
            });
        }

        storageNumber.appendChild(row);
    }
}

function renderDVDWriterRows() {
    const rowCount = Number(dvdWriterNumberSelect.value) || 1;

    dvdWriterNumber.innerHTML = '';

    for (let index = 1; index <= rowCount; index += 1) {
        const row = dvdWriterRowTemplate.cloneNode(true);
        updateDVDWriterFieldIds(row, index);

        if (index > 1) {
            row.querySelectorAll('select').forEach((select) => {
                select.selectedIndex = 0;
            });
        }

        dvdWriterNumber.appendChild(row);
    }
}

slotNumberSelect.addEventListener('change', renderRamRows);
hardNumberSelect.addEventListener('change', renderHardRows);
dvdWriterNumberSelect.addEventListener('change', renderDVDWriterRows);
dvdWriterEnabled.addEventListener('change', toggleDVDWriterSection);
toggleDVDWriterSection();
renderRamRows();
renderHardRows();
renderDVDWriterRows();