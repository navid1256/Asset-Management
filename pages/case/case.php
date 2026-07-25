<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__, 2) . '/bootstrap/constants.php';

if (empty($_SESSION['authenticated_user_id'])) {
    header('Location: ' . BASE_URL . '/pages/normal-login/normal-login.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (empty($_SESSION['receiver_user_id'])) {
    header('Location: ' . BASE_URL . '/pages/select-user/select-user.php');
    exit;
}

$formSuccess = $_SESSION['case_form_success'] ?? null;
$formError = $_SESSION['case_form_error'] ?? null;

unset($_SESSION['case_form_success'], $_SESSION['case_form_error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Info</title>
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/case.css">
    <link rel="icon" href="<?= ASSETS_URL ?>/img/logo.png" type="image">
</head>

<body>
    <header>
        <h1>نرم افزار مدیریت درخواست و ثبت تجهیزات فناوری</h1>
        <div class="user-profile">
            <img src="<?= ASSETS_URL ?>/img/profile-avatar.avif" alt="user profile" class="avatar">
            <div class="user-info">
                <p class="username">
                    <span>تحویل گیرنده :</span>
                    <span id="profile-full-name">نام کاربر</span>
                </p>
                <p class="userrole" id="profile-department">معاونت / اداره</p>
            </div>
        </div>
        <div class="logged-in-user">
            <span>کاربر :</span>
            <span id="logged-in-username">کاربر نامشخص</span>
        </div>
        <img src="<?= ASSETS_URL ?>/img/logo.png" alt="لوگو شرکت">
    </header>
    <?php if ($formSuccess): ?>
        <div class="form-message form-message-success" role="status">
            <?= htmlspecialchars($formSuccess, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($formError): ?>
        <div class="form-message form-message-error" role="alert">
            <?= htmlspecialchars($formError, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form id="case-info-form" action="<?= BASE_URL ?>/process/save-case.php" method="post"
        enctype="multipart/form-data">
        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <fieldset class="form-section">
            <legend>
                ثبت مشخصات تحویلی کیس
            </legend>
            <section class="it-numbers">
                <div class="shomare-amval">
                    <label for="asset-number"> شماره اموال :</label>
                    <input type="text" id="asset-number" name="assetNumber" placeholder="شماره اموال"
                        inputmode="numeric" maxlength="5" pattern="[۰-۹]{0,5}" autocomplete="off"
                        data-persian-number>
                </div>
                <div class="shomare-it">
                    <label for="it-number"> شماره IT :</label>
                    <input type="text" id="it-number" name="itNumber" placeholder="شماره IT" inputmode="numeric"
                        maxlength="4" pattern="[۰-۹]{1,4}" autocomplete="off" data-persian-number required>
                </div>
            </section>
        </fieldset>
        <fieldset class="form-section">
            <legend>ثبت مشخصات فنی کیس</legend>
            <section class="hardware">
                <section class="component">
                    <h2>CPU</h2>
                    <div class="cpu">
                        <div class="cpu brand">
                            <label for="cpu-brand">برند :</label>
                            <select name="cpu_brand" id="cpu-brand" required>
                                <option value="" disabled selected>Select Brand</option>
                                <option value="AMD">AMD</option>
                                <option value="INTEL">INTEL</option>
                            </select>
                        </div>
                        <div class="cpu gen">
                            <label for="cpu-gen">نسل :</label>
                            <select name="cpu_generation" id="cpu-gen" required>
                                <option value="" disabled selected>Select Gen</option>
                                <option value="7">Gen 7th</option>
                                <option value="8">Gen 8th</option>
                                <option value="9">Gen 9th</option>
                                <option value="10">Gen 10th</option>
                                <option value="11">Gen 11th</option>
                                <option value="12">Gen 12th</option>
                                <option value="13">Gen 13th</option>
                                <option value="14">Gen 14th</option>
                            </select>
                        </div>
                        <div class="cpu model">
                            <label for="cpu-model">مدل :</label>
                            <select name="cpu_model" id="cpu-model" required>
                                <option value="" disabled selected>Select Model</option>
                                <option value="Core i5-4670">Core i5-4670</option>
                                <option value="Core i5-4670K">Core i5-4670K</option>
                                <option value="Core i5-4690">Core i5-4690</option>
                                <option value="Core i5-4690K">Core i5-4690K</option>
                                <option value="Core i5-5675C">Core i5-5675C</option>
                            </select>
                        </div>
                        <div class="cpu speed">
                            <label for="cpu-speed">سرعت :</label>
                            <select name="cpu_speed_ghz" id="cpu-speed" required>
                                <option value="" disabled selected>Select Speed</option>
                                <option value="1.0">1.0 Ghz</option>
                                <option value="1.5">1.5 Ghz</option>
                                <option value="2.0">2.0 Ghz</option>
                                <option value="2.5">2.5 Ghz</option>
                                <option value="3.0">3.0 Ghz</option>
                                <option value="3.5">3.5 Ghz</option>
                                <option value="4.0">4.0 Ghz</option>
                            </select>
                        </div>
                    </div>
                    <div class="component-footer">
                        <div class="component-upload">
                            <label for="cpu-upload" class="component-upload-button">بارگذاری تصویر گارانتی</label>
                            <input type="file" id="cpu-upload" name="cpu_warranty_file" class="component-file-input"
                                accept="image/*,application/pdf">
                            <span class="component-file-name" data-file-name-for="cpu-upload" aria-live="polite">فایلی
                                انتخاب نشده</span>
                        </div>
                    </div>
                </section>
                <section class="component">
                    <h2>MotherBoard</h2>
                    <div class="motherboard">
                        <div class="motherboard brand">
                            <label for="motherboard-brand">برند :</label>
                            <select id="motherboard-brand" name="motherboard_brand" required>
                                <option value="" disabled selected>Select Brand</option>
                                <option value="ASUS">ASUS</option>
                                <option value="GIGABYTE">GIGABYTE</option>
                                <option value="Asrock">Asrock</option>
                                <option value="MSI">MSI</option>
                                <option value="EVGA">EVGA</option>
                                <option value="Biostar">Biostar</option>
                                <option value="Intel">Intel</option>
                            </select>
                        </div>
                        <div class="motherboard model">
                            <label for="motherboard-model">مدل :</label>
                            <select id="motherboard-model" name="motherboard_model" required>
                                <option value="" disabled selected>Select Model</option>
                                <option value="X-399 E-GAMING">X-399 E-GAMING</option>
                                <option value="PRIME H310-PLUS R2.0">PRIME H310-PLUS R2.0</option>
                                <option value="PRIME H310M-K">PRIME H310M-K</option>
                                <option value="B360M-DRAGON S">B360M-DRAGON S</option>
                                <option value="TUF GAMING Z490-PLUS">TUF GAMING Z490-PLUS</option>
                                <option value="TUF GAMING B560M-E">TUF GAMING B560M-E</option>
                                <option value="PRIME Z590-V">PRIME Z590-V</option>
                            </select>
                        </div>
                    </div>
                    <div class="component-footer">
                        <div class="component-upload">
                            <label for="motherboard-upload" class="component-upload-button">بارگذاری تصویر گارانتی</label>
                            <input type="file" id="motherboard-upload" name="motherboard_warranty_file" class="component-file-input"
                                accept="image/*,application/pdf">
                            <span class="component-file-name" data-file-name-for="motherboard-upload" aria-live="polite">فایلی
                                انتخاب نشده</span>
                        </div>
                    </div>
                </section>
                <section class="component">
                    <h2>GPU</h2>
                    <div class="gpu">
                        <div class="gpu option">
                            <div class="gpu-option">
                                <input type="radio" id="gpu-onboard" name="gpu_type" value="onboard" checked>
                                <label for="gpu-onboard">Onboard</label>
                            </div>
                            <div class="gpu-option">
                                <input type="radio" id="gpu-internal" name="gpu_type" value="internal">
                                <label for="gpu-internal">Internal</label>
                            </div>
                        </div>
                        <div class="gpu brand">
                            <label for="gpu-brand">برند :</label>
                            <select id="gpu-brand" name="gpu_brand">
                                <option value="" disabled selected>Select Brand</option>
                                <option value="ASUS">ASUS</option>
                                <option value="EVGA">EVGA</option>
                                <option value="GIGABYTE">GIGABYTE</option>
                                <option value="MSI">MSI</option>
                                <option value="ZOTAK">ZOTAK</option>
                                <option value="ASROCK">ASROCK</option>
                                <option value="INTEL">INTEL</option>
                                <option value="Saphire">Saphire</option>
                            </select>
                        </div>
                        <div class="gpu model">
                            <label for="gpu-model">مدل :</label>
                            <select id="gpu-model" name="gpu_model">
                                <option value="" disabled selected>Select Model</option>
                                <option value="GTX-1080">GTX-1080</option>
                                <option value="GTX-1070">GTX-1070</option>
                                <option value="GTX-1060">GTX-1060</option>
                                <option value="GTX-1050">GTX-1050</option>
                                <option value="GTX-1030">GTX-1030</option>
                                <option value="GTX-1660">GTX-1660</option>
                                <option value="RTX-2070">RTX-2070</option>
                                <option value="RTX-2060">RTX-2060</option>
                            </select>
                        </div>
                        <div class="gpu memory">
                            <label for="gpu-memory">حافظه :</label>
                            <select id="gpu-memory" name="gpu_memory_gb">
                                <option value="" disabled selected>Select Memory</option>
                                <option value="1 GB">1GB</option>
                                <option value="2 GB">2GB</option>
                                <option value="3 GB">3GB</option>
                                <option value="4 GB">4GB</option>
                                <option value="6 GB">6GB</option>
                                <option value="8 GB">8GB</option>
                                <option value="10 GB">10GB</option>
                                <option value="12 GB">12GB</option>
                            </select>
                        </div>
                    </div>
                    <div class="component-footer">
                        <div class="component-upload">
                            <label for="gpu-upload" class="component-upload-button">بارگذاری تصویر
                                گارانتی</label>
                            <input type="file" id="gpu-upload" name="gpu_warranty_file" class="component-file-input"
                                accept="image/*,application/pdf">
                            <span class="component-file-name" data-file-name-for="gpu-upload" aria-live="polite">فایلی
                                انتخاب نشده</span>
                        </div>
                    </div>
                </section>
                <section class="component">
                    <h2>RAM</h2>
                    <div class="ram">
                        <div class="ram slot-number">
                            <label for="slot-number">تعداد رم :</label>
                            <select name="ram_count" id="slot-number" required>
                                <option value="" disabled selected>Select Number</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="ram-slots" id="ram-slots">
                            <div class="ram-row" data-ram-row>
                                <div class="ram brand">
                                    <label for="ram-brand-1">برند :</label>
                                    <select id="ram-brand-1" name="ram_brand[]" required>
                                        <option value="" disabled selected>Select Brand</option>
                                        <option value="HyperX">HyperX</option>
                                        <option value="Corsair">Corsair</option>
                                        <option value="Kingston">Kingston</option>
                                        <option value="Crucial">Crucial</option>
                                        <option value="G.Skill">G.Skill</option>
                                        <option value="Samsung">Samsung</option>
                                        <option value="Team Group">Team Group</option>
                                        <option value="Patriot">Patriot</option>
                                    </select>
                                </div>
                                <div class="ram model">
                                    <label for="ram-model-1">مدل :</label>
                                    <select id="ram-model-1" name="ram_model[]" required>
                                        <option value="" disabled selected>Select Model</option>
                                        <option value="FURY Beast">FURY Beast</option>
                                        <option value="Vengeance">Vengeance</option>
                                        <option value="Vengeance LPX">Vengeance LPX</option>
                                        <option value="Trident Z5 Neo RGB">Trident Z5 Neo RGB</option>
                                        <option value="Trident Z5 RGB">Trident Z5 RGB</option>
                                        <option value="T-Force Xtreem ARGB">T-Force Xtreem ARGB</option>
                                        <option value="Viper Steel">Viper Steel</option>
                                        <option value="Viper 4">Viper 4</option>
                                        <option value="Viper RGB">Viper RGB</option>
                                        <option value="Aegis">Aegis</option>
                                    </select>
                                </div>
                                <div class="ram module">
                                    <label for="ram-module-1">ماژول حافظه :</label>
                                    <select id="ram-module-1" name="ram_type[]" required>
                                        <option value="" disabled selected>Module</option>
                                        <option value="DDR4">DDR4</option>
                                        <option value="DDR5">DDR5</option>
                                    </select>
                                </div>
                                <div class="ram capacity">
                                    <label for="ram-capacity-1">مقدار حافظه :</label>
                                    <select name="ram_capacity_gb[]" id="ram-capacity-1" required>
                                        <option value="" disabled selected>Capacity</option>
                                        <option value="1">1GB</option>
                                        <option value="2">2GB</option>
                                        <option value="4">4GB</option>
                                        <option value="6">6GB</option>
                                        <option value="8">8GB</option>
                                        <option value="16">16GB</option>
                                    </select>
                                </div>
                                <div class="ram speed">
                                    <label for="memory-speed-1">سرعت حافظه :</label>
                                    <input type="number" id="memory-speed-1" name="ram_speed_mhz[]" min="1" step="1"
                                        placeholder="MHz" required>
                                    <span class="speed-unit">MHz</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="component-footer">
                        <div class="component-upload">
                            <label for="ram-upload" class="component-upload-button">بارگذاری تصویر گارانتی</label>
                            <input type="file" id="ram-upload" name="ram_warranty_file" class="component-file-input"
                                accept="image/*,application/pdf">
                            <span class="component-file-name" data-file-name-for="ram-upload" aria-live="polite">فایلی
                                انتخاب نشده</span>
                        </div>

                        <div class="component-total">
                            <span>ظرفیت کل RAM :</span>
                            <output id="total-ram-capacity">0GB</output>
                        </div>
                    </div>
                </section>
                <section class="component">
                    <h2>Storage</h2>
                    <div class="storage">
                        <div class="hard number">
                            <label for="hard-number">تعداد هارد ها :</label>
                            <select name="storage_count" id="hard-number" required>
                                <option value="" disabled selected>Select Number</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="storage-number" id="storage-number">
                            <div class="hard-row" data-hard-row>
                                <div class="hard type">
                                    <label for="hard-type-1">نوع :</label>
                                    <select id="hard-type-1" name="storage_type[]" required>
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="NVMe">NVMe</option>
                                        <option value="M.2 SATA">M.2 SATA</option>
                                        <option value="SATA">SATA</option>
                                    </select>
                                </div>
                                <div class="hard brand">
                                    <label for="hard-brand-1">برند :</label>
                                    <select id="hard-brand-1" name="storage_brand[]" required>
                                        <option value="" disabled selected>Select Brand</option>
                                        <option value="Western Digital">Western Digital</option>
                                        <option value="Seagate">Seagate</option>
                                        <option value="Samsung">Samsung</option>
                                        <option value="Kingston">Kingston</option>
                                        <option value="Crucial">Crucial</option>
                                        <option value="ADATA">ADATA</option>
                                        <option value="Lexar">Lexar</option>
                                        <option value="SanDisk">SanDisk</option>
                                        <option value="Toshiba">Toshiba</option>
                                        <option value="Kioxia">Kioxia</option>
                                        <option value="Team Group">Team Group</option>
                                        <option value="Patriot">Patriot</option>
                                        <option value="Corsair">Corsair</option>
                                        <option value="Silicon Power">Silicon Power</option>
                                        <option value="HP">HP</option>
                                    </select>
                                </div>
                                <div class="hard model">
                                    <label for="hard-model-1">مدل :</label>
                                    <select id="hard-model-1" name="storage_model[]" required>
                                        <option value="" disabled selected>Select Model</option>
                                        <option value="WD Blue">WD Blue</option>
                                        <option value="WD Green">WD Green</option>
                                        <option value="WD Black SN750">WD Black SN750</option>
                                        <option value="WD Black SN770">WD Black SN770</option>
                                        <option value="WD Black SN850X">WD Black SN850X</option>
                                        <option value="WD Red Plus">WD Red Plus</option>
                                        <option value="Seagate BarraCuda">Seagate BarraCuda</option>
                                        <option value="Seagate FireCuda">Seagate FireCuda</option>
                                        <option value="Seagate IronWolf">Seagate IronWolf</option>
                                        <option value="Samsung 860 EVO">Samsung 860 EVO</option>
                                        <option value="Samsung 870 EVO">Samsung 870 EVO</option>
                                        <option value="Samsung 970 EVO Plus">Samsung 970 EVO Plus</option>
                                        <option value="Samsung 980">Samsung 980</option>
                                        <option value="Samsung 980 PRO">Samsung 980 PRO</option>
                                        <option value="Samsung 990 PRO">Samsung 990 PRO</option>
                                        <option value="Kingston A400">Kingston A400</option>
                                        <option value="Kingston NV2">Kingston NV2</option>
                                        <option value="Kingston KC3000">Kingston KC3000</option>
                                        <option value="Crucial BX500">Crucial BX500</option>
                                        <option value="Crucial MX500">Crucial MX500</option>
                                        <option value="Crucial P3">Crucial P3</option>
                                        <option value="Crucial P5 Plus">Crucial P5 Plus</option>
                                        <option value="ADATA SU650">ADATA SU650</option>
                                        <option value="ADATA Legend 800">ADATA Legend 800</option>
                                        <option value="XPG SX8200 Pro">XPG SX8200 Pro</option>
                                        <option value="Lexar NM620">Lexar NM620</option>
                                        <option value="Lexar NM710">Lexar NM710</option>
                                        <option value="Lexar NM790">Lexar NM790</option>
                                        <option value="SanDisk SSD Plus">SanDisk SSD Plus</option>
                                        <option value="SanDisk Ultra 3D">SanDisk Ultra 3D</option>
                                        <option value="Toshiba P300">Toshiba P300</option>
                                        <option value="Toshiba X300">Toshiba X300</option>
                                        <option value="Kioxia Exceria">Kioxia Exceria</option>
                                        <option value="Team Group GX2">Team Group GX2</option>
                                        <option value="Team Group MP33">Team Group MP33</option>
                                        <option value="Team Group MP44">Team Group MP44</option>
                                        <option value="Patriot Burst Elite">Patriot Burst Elite</option>
                                        <option value="Patriot P300">Patriot P300</option>
                                        <option value="Corsair MP510">Corsair MP510</option>
                                        <option value="Corsair MP600">Corsair MP600</option>
                                        <option value="Silicon Power A55">Silicon Power A55</option>
                                        <option value="Silicon Power P34A60">Silicon Power P34A60</option>
                                        <option value="HP S700">HP S700</option>
                                        <option value="HP EX900">HP EX900</option>
                                    </select>
                                </div>
                                <div class="hard capacity">
                                    <label for="hard-capacity-1">ظرفیت :</label>
                                    <select name="storage_capacity_gb[]" id="hard-capacity-1" required>
                                        <option value="" disabled selected>Capacity</option>
                                        <option value="125">125GB</option>
                                        <option value="256">256GB</option>
                                        <option value="512">512GB</option>
                                        <option value="1024">1TB</option>
                                        <option value="2048">2TB</option>
                                        <option value="3072">3TB</option>
                                        <option value="4096">4TB</option>
                                        <option value="5120">5TB</option>
                                    </select>
                                </div>
                                <div class="hard warranty">
                                    <div class="component-upload hard-warranty">
                                        <label for="storage-upload-1" class="component-upload-button">بارگذاری تصویر
                                            گارانتی</label>
                                        <input type="file" id="storage-upload-1" name="storage_warranty_files[]"
                                            class="component-file-input storage-warranty-input"
                                            accept="image/*,application/pdf">
                                        <span class="component-file-name" data-storage-file-name
                                            aria-live="polite">فایلی انتخاب نشده</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="component-footer storage-footer">
                        <div class="component-total">
                            <span>ظرفیت کل Storage :</span>
                            <output id="total-storage-capacity">0GB</output>
                        </div>
                    </div>
                </section>
                <section class="component">
                    <label class="dvd-writer-toggle" for="dvd-writer-enabled">
                        <input type="checkbox" id="dvd-writer-enabled" name="writer_enabled" value="1">
                        <h2>Writer</h2>
                    </label>

                    <div class="dvd-writer dvd-writer-section">
                        <div class="writer-option">
                            <div class="cd-writer">
                                <input type="radio" id="cd-writer" name="writer_type" value="CD Writer" checked>
                                <label for="cd-writer">CD Writer</label>
                            </div>
                            <div class="dvd-writer">
                                <input type="radio" id="dvd-writer" name="writer_type" value="DVD Writer">
                                <label for="dvd-writer">DVD Writer</label>
                            </div>
                        </div>
                        <div class="dvd-writer-number" id="dvd-writer-numbers">
                            <div class="dvd-writer-row" data-dvd-writer-row>
                                <div class="dvd-writer brand">
                                    <label for="dvd-writer-brand-1">Brand</label>
                                    <select name="writer_brand[]" id="dvd-writer-brand-1">
                                        <option value="" disabled selected>Select Brand</option>
                                        <option value="ASUS">ASUS</option>
                                        <option value="LG">LG</option>
                                        <option value="Lite-On">Lite-On</option>
                                        <option value="Pioneer">Pioneer</option>
                                        <option value="Sony Optiarc">Sony Optiarc</option>
                                        <option value="Samsung">Samsung</option>
                                        <option value="HP">HP</option>
                                        <option value="Dell">Dell</option>
                                        <option value="Lenovo">Lenovo</option>
                                        <option value="Plextor">Plextor</option>
                                    </select>

                                </div>
                                <div class="dvd-writer model">
                                    <label for="dvd-writer-model-1">Model</label>
                                    <select name="writer_model[]" id="dvd-writer-model-1">
                                        <option value="" disabled selected>Select Model</option>
                                        <option value="ASUS DRW-24D5MT">ASUS DRW-24D5MT</option>
                                        <option value="ASUS DRW-24F1ST">ASUS DRW-24F1ST</option>
                                        <option value="ASUS DRW-24B1ST">ASUS DRW-24B1ST</option>
                                        <option value="LG GH24NSC0">LG GH24NSC0</option>
                                        <option value="LG GH24NSD5">LG GH24NSD5</option>
                                        <option value="LG GH24NSD1">LG GH24NSD1</option>
                                        <option value="Lite-On iHAS124">Lite-On iHAS124</option>
                                        <option value="Lite-On iHAS324">Lite-On iHAS324</option>
                                        <option value="Pioneer DVR-221BK">Pioneer DVR-221BK</option>
                                        <option value="Pioneer DVR-S21WBK">Pioneer DVR-S21WBK</option>
                                        <option value="Sony Optiarc AD-7280S">Sony Optiarc AD-7280S</option>
                                        <option value="Sony Optiarc AD-7260S">Sony Optiarc AD-7260S</option>
                                        <option value="Samsung SH-224DB">Samsung SH-224DB</option>
                                        <option value="Samsung SH-224FB">Samsung SH-224FB</option>
                                        <option value="HP DH16ACSH">HP DH16ACSH</option>
                                        <option value="HP GH60L">HP GH60L</option>
                                        <option value="Dell DW316">Dell DW316</option>
                                        <option value="Dell GTA0N">Dell GTA0N</option>
                                        <option value="Lenovo GUE0N">Lenovo GUE0N</option>
                                        <option value="Plextor PX-891SA">Plextor PX-891SA</option>
                                    </select>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="component-footer">
                        <div class="component-upload">
                            <label for="writer-upload" class="component-upload-button">بارگذاری تصویر گارانتی</label>
                            <input type="file" id="writer-upload" name="writer_warranty_file" class="component-file-input"
                                accept="image/*,application/pdf">
                            <span class="component-file-name" data-file-name-for="writer-upload"
                                aria-live="polite">فایلی انتخاب نشده</span>
                        </div>
                    </div>
                </section>
                <section class="component">
                    <h2>Power</h2>
                    <div class="power">
                        <div class="power brand">
                            <label for="power-brand">برند :</label>
                            <select name="power_brand" id="power-brand" required>
                                <option value="" disabled selected>Select Brand</option>
                                <option value="Corsair">Corsair</option>
                                <option value="EVGA">EVGA</option>
                                <option value="Seasonic">Seasonic</option>
                                <option value="Cooler Master">Cooler Master</option>
                                <option value="Thermaltake">Thermaltake</option>
                                <option value="be quiet!">be quiet!</option>
                                <option value="NZXT">NZXT</option>
                                <option value="Antec">Antec</option>
                            </select>
                        </div>
                        <div class="power model">
                            <label for="power-model">مدل :</label>
                            <select name="power_model" id="power-model" required>
                                <option value="" disabled selected>Select Model</option>
                                <option value="RM750x">RM750x</option>
                                <option value="RM850x">RM850x</option>
                                <option value="RM1000x">RM1000x</option>
                                <option value="SuperNOVA 750 G5">SuperNOVA 750 G5</option>
                                <option value="SuperNOVA 850 G5">SuperNOVA 850 G5</option>
                                <option value="SuperNOVA 1000 G5">SuperNOVA 1000 G5</option>
                                <option value="Focus GX-750">Focus GX-750</option>
                                <option value="Focus GX-850">Focus GX-850</option>
                            </select>
                        </div>
                        <div class="power wattage">
                            <label for="power-wattage">توان :</label>
                            <select name="power_wattage_w" id="power-wattage" required>
                                <option value="" disabled selected>Select Wattage</option>
                                <option value="500">500W</option>
                                <option value="600">600W</option>
                                <option value="650">650W</option>
                                <option value="700">700W</option>
                                <option value="750">750W</option>
                                <option value="800">800W</option>
                                <option value="850">850W</option>
                                <option value="1000">1000W</option>
                            </select>
                        </div>
                    </div>
                    <div class="component-footer">
                        <div class="component-upload">
                            <label for="power-upload" class="component-upload-button">بارگذاری تصویر گارانتی</label>
                            <input type="file" id="power-upload" name="power_warranty_file" class="component-file-input"
                                accept="image/*,application/pdf">
                            <span class="component-file-name" data-file-name-for="power-upload" aria-live="polite">فایلی
                                انتخاب نشده</span>
                        </div>
                    </div>
                </section>
                <section class="component">
                    <h2>Case</h2>
                    <div class="case">
                        <div class="case brand">
                            <label for="case-brand">برند :</label>
                            <select name="case_brand" id="case-brand" required>
                                <option value="" disabled selected>Select Brand</option>
                                <option value="Corsair">Corsair</option>
                                <option value="EVGA">EVGA</option>
                                <option value="Seasonic">Seasonic</option>
                                <option value="Cooler Master">Cooler Master</option>
                                <option value="Thermaltake">Thermaltake</option>
                                <option value="be quiet!">be quiet!</option>
                                <option value="NZXT">NZXT</option>
                                <option value="Antec">Antec</option>
                            </select>
                        </div>
                        <div class="case model">
                            <label for="case-model">مدل :</label>
                            <select name="case_model" id="case-model" required>
                                <option value="" disabled selected>Select Model</option>
                                <option value="RM750x">RM750x</option>
                                <option value="RM850x">RM850x</option>
                                <option value="RM1000x">RM1000x</option>
                                <option value="SuperNOVA 750 G5">SuperNOVA 750 G5</option>
                                <option value="SuperNOVA 850 G5">SuperNOVA 850 G5</option>
                                <option value="SuperNOVA 1000 G5">SuperNOVA 1000 G5</option>
                                <option value="Focus GX-750">Focus GX-750</option>
                                <option value="Focus GX-850">Focus GX-850</option>
                            </select>
                        </div>
                    </div>
                    <div class="component-footer">
                        <div class="component-upload">
                            <label for="case-upload" class="component-upload-button">بارگذاری تصویر گارانتی</label>
                            <input type="file" id="case-upload" name="case_warranty_file" class="component-file-input"
                                accept="image/*,application/pdf">
                            <span class="component-file-name" data-file-name-for="case-upload" aria-live="polite">فایلی
                                انتخاب نشده</span>
                        </div>
                    </div>
                </section>
            </section>




        </fieldset>
        <div class="form-actions">
            <button type="submit" class="submit-button">ثبت</button>
            <div class="delivery-upload">
                <label for="delivery-sheet" class="upload-button">بارگذاری برگه تحویل</label>
                <input type="file" id="delivery-sheet" name="delivery_sheet" class="file-input"
                    accept="image/*,application/pdf"
                    required>
                <span id="delivery-file-name" class="file-name" aria-live="polite">فایلی انتخاب نشده</span>
            </div>
        </div>
    </form>
    <script src="<?= ASSETS_URL ?>/js/user-profile.js?v=<?= filemtime(BASE_PATH . '/assets/js/user-profile.js') ?>"></script>
    <script src="<?= ASSETS_URL ?>/js/persian-digits.js?v=<?= filemtime(BASE_PATH . '/assets/js/persian-digits.js') ?>"></script>
    <script src="<?= ASSETS_URL ?>/js/case.js"></script>


</body>

</html>
