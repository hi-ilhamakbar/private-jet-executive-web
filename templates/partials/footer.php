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
        <div><a class="footer__brand" href="/">Private Jet Executive</a><p>Commercial Park Aeropolis Apartments, Jl. Aeropolis Tower A Blok DF K. 17, RT.004/RW.008, Neglasari, Kec. Neglasari, Kota Tangerang, Banten 15129</p><div class="footer__payments"><p class="eyebrow">We Accept</p><div class="payment-methods" aria-label="Accepted payment methods"><span class="payment-logo"><img src="/assets/images/payment/visa.svg" alt="Visa"></span><span class="payment-logo"><img src="/assets/images/payment/mastercard.svg" alt="Mastercard"></span><span class="payment-logo"><img src="/assets/images/payment/jcb.svg" alt="JCB"></span><span class="payment-logo"><img src="/assets/images/payment/amex.svg" alt="American Express"></span><span class="payment-logo"><img src="/assets/images/payment/unionpay.svg" alt="UnionPay"></span><span class="payment-logo payment-logo--transfer" role="img" aria-label="Bank Transfer">Bank<br>Transfer</span></div></div></div>
        <div class="footer__connect"><p class="eyebrow">Connect</p><a href="mailto:charter@privatejetexecutive.com">charter@privatejetexecutive.com</a><a href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">WhatsApp: 0882 91 7777 00 (landline available)</a></div>
        <div class="footer__links"><a href="/private-charter">Private Charter</a><a href="/services">Services</a><a href="/contact">Contact Us</a><a href="/privacy">Privacy</a><a href="/terms">Terms</a></div>
    </div>
    <div class="container footer__bottom"><small>Copyright © <?= date('Y') ?> Private Jet Executive. All rights reserved.</small><span>Global reach. Personal service.</span></div>
</footer>
