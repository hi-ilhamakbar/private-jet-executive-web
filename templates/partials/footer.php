<?php
$whatsappMessage = <<<'TEXT'
Hello Private Jet Executive Team,

I would appreciate information regarding a private jet charter for the following proposed journey:

Route:
Preferred travel date:
Journey type (one-way / return):
Total number of passengers:
Additional requirements:

Thank you. I look forward to your response.
TEXT;
$whatsappUrl = 'https://wa.me/6288291777700?text=' . rawurlencode($whatsappMessage);
?>
<footer class="site-footer">
    <div class="container footer__inner">
        <div><a class="footer__brand" href="/">Private Jet Executive</a><p>Commercial Park Aeropolis Apartments, Jl. Aeropolis Tower A Blok DF K. 17, RT.004/RW.008, Neglasari, Kec. Neglasari, Kota Tangerang, Banten 15129</p><p class="footer__payment-copy">For your convenience, we accept Visa, Mastercard, JCB, American Express, UnionPay, and bank transfer.</p><div class="payment-methods" aria-label="Accepted payment methods"><span class="payment-mark payment-mark--visa" role="img" aria-label="Visa"><svg viewBox="0 0 48 16" aria-hidden="true"><text x="1" y="12" font-family="Arial,sans-serif" font-size="12" font-weight="700" font-style="italic">VISA</text></svg></span><span class="payment-mark payment-mark--mastercard" role="img" aria-label="Mastercard"><svg viewBox="0 0 32 16" aria-hidden="true"><circle cx="11" cy="8" r="6" fill="#eb001b"/><circle cx="21" cy="8" r="6" fill="#f79e1b"/><path d="M16 3.7a6 6 0 0 0 0 8.6 6 6 0 0 0 0-8.6z" fill="#ff5f00"/></svg></span><span class="payment-mark payment-mark--jcb" role="img" aria-label="JCB"><svg viewBox="0 0 32 16" aria-hidden="true"><rect x="1" y="1" width="30" height="14" rx="2" fill="#fff"/><text x="5" y="12" fill="#1a4d9b" font-family="Arial,sans-serif" font-size="10" font-weight="700">JCB</text></svg></span><span class="payment-mark payment-mark--amex" role="img" aria-label="American Express"><svg viewBox="0 0 44 16" aria-hidden="true"><rect x="1" y="1" width="42" height="14" rx="1" fill="#2378b9"/><text x="4" y="11" fill="#fff" font-family="Arial,sans-serif" font-size="7" font-weight="700">AMEX</text></svg></span><span class="payment-mark payment-mark--unionpay" role="img" aria-label="UnionPay"><svg viewBox="0 0 48 16" aria-hidden="true"><rect x="1" y="1" width="46" height="14" rx="2" fill="#fff"/><text x="4" y="11" fill="#e32219" font-family="Arial,sans-serif" font-size="8" font-weight="700">Union</text><text x="27" y="11" fill="#178047" font-family="Arial,sans-serif" font-size="8" font-weight="700">Pay</text></svg></span><span class="payment-mark payment-mark--transfer" role="img" aria-label="Bank transfer">Bank<br>transfer</span></div></div>
        <div class="footer__connect"><p class="eyebrow">Connect</p><a href="mailto:charter@privatejetexecutive.com">charter@privatejetexecutive.com</a><a href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">WhatsApp: 0882 91 7777 00 (landline available)</a></div>
        <div class="footer__links"><a href="/private-charter">Private Charter</a><a href="/services">Services</a><a href="/contact">Contact Us</a><a href="/privacy">Privacy</a><a href="/terms">Terms</a></div>
    </div>
    <div class="container footer__bottom"><small>Copyright © <?= date('Y') ?> Private Jet Executive. All rights reserved.</small><span>Global reach. Personal service.</span></div>
</footer>
