<?php
$whatsappMessage = <<<'TEXT'
Hi Private Jet Executive! Saya ingin meminta info mengenai kebutuhan private jet dengan jadwal sebagai berikut:

Rute:
Tanggal:
One way / return:
Total pax:
Additional request:

Thank you
TEXT;
$whatsappUrl = 'https://wa.me/6288291777700?text=' . rawurlencode($whatsappMessage);
?>
<footer class="site-footer">
    <div class="container footer__inner">
        <div><a class="footer__brand" href="/">Private Jet Executive</a><p>Commercial Park Aeropolis Apartments, Jl. Aeropolis Tower A Blok DF K. 17, RT.004/RW.008, Neglasari, Kec. Neglasari, Kota Tangerang, Banten 15129</p></div>
        <div class="footer__connect"><p class="eyebrow">Connect</p><a href="mailto:charter@privatejetexecutive.com">charter@privatejetexecutive.com</a><a href="<?= htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">WhatsApp: 0882 91 7777 00</a><a href="tel:+6288291777700">Phone / Landline: 0882 91 7777 00</a></div>
        <div class="footer__links"><a href="/private-charter">Private Charter</a><a href="/services">Services</a><a href="/contact">Contact Us</a><a href="/privacy">Privacy</a><a href="/terms">Terms</a></div>
    </div>
    <div class="container footer__bottom"><small>Copyright © <?= date('Y') ?> Private Jet Executive. All rights reserved.</small><span>Global reach. Personal service.</span></div>
</footer>
