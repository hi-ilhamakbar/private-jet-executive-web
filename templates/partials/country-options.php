<?php

declare(strict_types=1);

$countries = [
    '+61' => ['au', 'Australia (+61)'], '+43' => ['at', 'Austria (+43)'], '+32' => ['be', 'Belgium (+32)'], '+55' => ['br', 'Brazil (+55)'],
    '+1' => ['us', 'United States / Canada (+1)'], '+86' => ['cn', 'China (+86)'], '+45' => ['dk', 'Denmark (+45)'], '+20' => ['eg', 'Egypt (+20)'],
    '+358' => ['fi', 'Finland (+358)'], '+33' => ['fr', 'France (+33)'], '+49' => ['de', 'Germany (+49)'], '+30' => ['gr', 'Greece (+30)'],
    '+852' => ['hk', 'Hong Kong (+852)'], '+91' => ['in', 'India (+91)'], '+62' => ['id', 'Indonesia (+62)'], '+353' => ['ie', 'Ireland (+353)'],
    '+972' => ['il', 'Israel (+972)'], '+39' => ['it', 'Italy (+39)'], '+81' => ['jp', 'Japan (+81)'], '+60' => ['my', 'Malaysia (+60)'],
    '+52' => ['mx', 'Mexico (+52)'], '+31' => ['nl', 'Netherlands (+31)'], '+64' => ['nz', 'New Zealand (+64)'], '+47' => ['no', 'Norway (+47)'],
    '+92' => ['pk', 'Pakistan (+92)'], '+63' => ['ph', 'Philippines (+63)'], '+48' => ['pl', 'Poland (+48)'], '+974' => ['qa', 'Qatar (+974)'],
    '+966' => ['sa', 'Saudi Arabia (+966)'], '+65' => ['sg', 'Singapore (+65)'], '+27' => ['za', 'South Africa (+27)'], '+82' => ['kr', 'South Korea (+82)'],
    '+34' => ['es', 'Spain (+34)'], '+46' => ['se', 'Sweden (+46)'], '+41' => ['ch', 'Switzerland (+41)'], '+886' => ['tw', 'Taiwan (+886)'],
    '+66' => ['th', 'Thailand (+66)'], '+90' => ['tr', 'Türkiye (+90)'], '+971' => ['ae', 'United Arab Emirates (+971)'], '+44' => ['gb', 'United Kingdom (+44)'],
];

foreach ($countries as $code => [$flag, $label]): ?>
    <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" data-flag="<?= htmlspecialchars($flag, ENT_QUOTES, 'UTF-8') ?>" <?= ($selectedCountry ?? '') === $code ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach;
