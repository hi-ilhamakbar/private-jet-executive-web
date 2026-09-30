<?php
/** @var array<string, mixed> $invoice */
/** @var callable $escape */
/** @var string $amount */
/** @var \DateTimeImmutable $dueAt */
?>
<!doctype html>
<html lang="en">
<body style="margin:0;padding:0;background:#f3eee5;color:#1b1d21;font-family:Arial,Helvetica,sans-serif;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background:#f3eee5;padding:28px 12px;"><tr><td align="center">
    <table role="presentation" width="620" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:620px;background:#fffdfa;border:1px solid #ded5c7;">
      <tr><td style="padding:26px 30px;background:#0a0f1c;color:#f8f7f4;"><p style="margin:0 0 7px;color:#d4af7c;font-size:11px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">Private Jet Executive</p><h1 style="margin:0;font-family:Georgia,serif;font-size:28px;font-weight:normal;">Your invoice is ready</h1></td></tr>
      <tr><td style="padding:28px 30px 18px;"><p style="margin:0 0 16px;font-size:16px;line-height:1.55;">Dear <?= $escape($invoice['invoice_recipient']) ?>,</p><p style="margin:0 0 20px;font-size:15px;line-height:1.55;">Please find your Private Jet Executive invoice attached. A summary is included below for your convenience.</p>
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #ded5c7;border-collapse:collapse;font-size:14px;"><tr><td style="padding:11px 14px;border-bottom:1px solid #ded5c7;color:#6a5c4b;width:40%;">Invoice</td><td style="padding:11px 14px;border-bottom:1px solid #ded5c7;font-weight:bold;"><?= $escape($invoice['invoice_number']) ?></td></tr><tr><td style="padding:11px 14px;border-bottom:1px solid #ded5c7;color:#6a5c4b;">Route</td><td style="padding:11px 14px;border-bottom:1px solid #ded5c7;"><?= $escape($invoice['route']) ?></td></tr><tr><td style="padding:11px 14px;border-bottom:1px solid #ded5c7;color:#6a5c4b;">Aircraft</td><td style="padding:11px 14px;border-bottom:1px solid #ded5c7;"><?= $escape($invoice['aircraft_type']) ?></td></tr><tr><td style="padding:13px 14px;background:#f3e1c5;color:#5d3d16;font-weight:bold;">Payment due</td><td style="padding:13px 14px;background:#f3e1c5;color:#5d3d16;font-weight:bold;"><?= $escape($dueAt->format('l, d F Y; H:i')) ?> WIB</td></tr><tr><td style="padding:13px 14px;color:#6a5c4b;font-weight:bold;">Total due</td><td style="padding:13px 14px;font-size:18px;font-weight:bold;"><?= $escape($amount) ?></td></tr></table>
        <p style="margin:22px 0 0;font-size:14px;line-height:1.55;">For payment questions, please reply to <a href="mailto:charter@privatejetexecutive.com" style="color:#8a5f28;">charter@privatejetexecutive.com</a>.</p><p style="margin:22px 0 0;font-size:14px;line-height:1.55;">Kind regards,<br>Private Jet Executive</p>
      </td></tr>
    </table>
  </td></tr></table>
</body>
</html>
