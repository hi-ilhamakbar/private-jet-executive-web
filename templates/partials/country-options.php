<?php

declare(strict_types=1);

$countries = [
    '+61' => '🇦🇺 Australia (+61)', '+43' => '🇦🇹 Austria (+43)', '+32' => '🇧🇪 Belgium (+32)', '+55' => '🇧🇷 Brazil (+55)',
    '+1' => '🇨🇦 Canada / United States (+1)', '+86' => '🇨🇳 China (+86)', '+45' => '🇩🇰 Denmark (+45)', '+20' => '🇪🇬 Egypt (+20)',
    '+358' => '🇫🇮 Finland (+358)', '+33' => '🇫🇷 France (+33)', '+49' => '🇩🇪 Germany (+49)', '+30' => '🇬🇷 Greece (+30)',
    '+852' => '🇭🇰 Hong Kong (+852)', '+91' => '🇮🇳 India (+91)', '+62' => '🇮🇩 Indonesia (+62)', '+353' => '🇮🇪 Ireland (+353)',
    '+972' => '🇮🇱 Israel (+972)', '+39' => '🇮🇹 Italy (+39)', '+81' => '🇯🇵 Japan (+81)', '+60' => '🇲🇾 Malaysia (+60)',
    '+52' => '🇲🇽 Mexico (+52)', '+31' => '🇳🇱 Netherlands (+31)', '+64' => '🇳🇿 New Zealand (+64)', '+47' => '🇳🇴 Norway (+47)',
    '+92' => '🇵🇰 Pakistan (+92)', '+63' => '🇵🇭 Philippines (+63)', '+48' => '🇵🇱 Poland (+48)', '+974' => '🇶🇦 Qatar (+974)',
    '+966' => '🇸🇦 Saudi Arabia (+966)', '+65' => '🇸🇬 Singapore (+65)', '+27' => '🇿🇦 South Africa (+27)', '+82' => '🇰🇷 South Korea (+82)',
    '+34' => '🇪🇸 Spain (+34)', '+46' => '🇸🇪 Sweden (+46)', '+41' => '🇨🇭 Switzerland (+41)', '+886' => '🇹🇼 Taiwan (+886)',
    '+66' => '🇹🇭 Thailand (+66)', '+90' => '🇹🇷 Türkiye (+90)', '+971' => '🇦🇪 United Arab Emirates (+971)', '+44' => '🇬🇧 United Kingdom (+44)',
];

foreach ($countries as $code => $label): ?>
    <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" <?= ($selectedCountry ?? '') === $code ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
<?php endforeach;
