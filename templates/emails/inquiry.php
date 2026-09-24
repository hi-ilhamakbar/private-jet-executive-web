<?php

declare(strict_types=1);

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<body style="margin:0;padding:0;background:#f1eee9;font-family:Arial,sans-serif;color:#1b1d21;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f1eee9;padding:28px 12px;"><tr><td align="center">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:620px;background:#f8f7f4;">
      <tr><td style="padding:30px 36px;background:#0a0f1c;color:#f8f7f4;">
        <div style="font-family:Georgia,serif;font-size:26px;letter-spacing:.04em;">Private Jet Executive</div>
        <div style="margin-top:8px;color:#d4af7c;font-size:11px;letter-spacing:.16em;text-transform:uppercase;">Global reach · Personal service</div>
      </td></tr>
      <tr><td style="padding:34px 36px 16px;">
        <div style="color:#b58b54;font-size:11px;font-weight:bold;letter-spacing:.15em;text-transform:uppercase;">Inquiry reference · <?= $escape($reference) ?></div>
        <h1 style="margin:14px 0 16px;font-family:Georgia,serif;font-size:34px;font-weight:normal;line-height:1.15;color:#0a0f1c;"><?= $escape($heading) ?></h1>
        <p style="margin:0 0 24px;font-size:15px;line-height:1.65;"><?= $escape($intro) ?></p>
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #ded8cf;">
          <?php foreach ($details as $label => $value): ?>
          <tr><td style="width:38%;padding:11px 14px;border-bottom:1px solid #ded8cf;color:#696a6e;font-size:12px;font-weight:bold;letter-spacing:.04em;vertical-align:top;"><?= $escape($label) ?></td><td style="padding:11px 14px;border-bottom:1px solid #ded8cf;color:#1b1d21;font-size:14px;line-height:1.5;white-space:pre-line;"><?= $escape($value) ?></td></tr>
          <?php endforeach; ?>
        </table>
        <p style="margin:24px 0 0;color:#505156;font-size:12px;line-height:1.6;"><?= $escape($disclaimer) ?></p>
      </td></tr>
      <tr><td style="padding:22px 36px;background:#0a0f1c;color:#eadcc8;font-size:12px;line-height:1.6;">PrivateJetExecutive.com<br>Ruko Jl. Pandanaran No. 1C Kav. 9, Pekunden, Kec. Semarang Tengah, Kota Semarang, Jawa Tengah - Indonesia 50134<br><a href="mailto:charter@privatejetexecutive.com" style="color:#d4af7c;text-decoration:none;">charter@privatejetexecutive.com</a></td></tr>
    </table>
  </td></tr></table>
</body>
</html>
