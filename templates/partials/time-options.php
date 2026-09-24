<?php

declare(strict_types=1);

$selectedTime = $selectedTime ?? '';

for ($hour = 0; $hour < 24; $hour++):
    for ($minute = 0; $minute < 60; $minute += 5):
        $time = sprintf('%02d:%02d', $hour, $minute);
?>
    <option value="<?= $time ?>" <?= $selectedTime === $time ? 'selected' : '' ?>><?= $time ?></option>
<?php
    endfor;
endfor;
