<?php
$old = $formState['old'];
$errors = $formState['errors'];
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<section class="page-hero">
    <div class="container page-hero__content">
        <p class="eyebrow">Contact Private Jet Executive</p>
        <h1>Start with a conversation.</h1>
        <p class="lead">For a private charter requirement, share the outline of your journey with our team. We will review the details and discuss an appropriate next step.</p>
    </div>
</section>

<section class="section section--pearl">
    <div class="container contact-layout">
        <div>
            <p class="eyebrow eyebrow--dark">Charter enquiries</p>
            <h2>Tell us where you need to be.</h2>
            <div class="prose"><p>Include your proposed departure and arrival points, preferred dates, passenger numbers and any details that are important to your journey. A member of our team will respond personally.</p><p>PrivateJetExecutive.com coordinates charter solutions from Indonesia to destinations worldwide. All arrangements remain subject to availability and final confirmation.</p></div>
        </div>
        <aside class="contact-card" aria-label="Contact details"><p class="eyebrow eyebrow--dark">Email our team</p><a class="contact-card__email" href="mailto:charter@privatejetexecutive.com">charter@privatejetexecutive.com</a><p>For private charter enquiries and travel requirements.</p><a class="button" href="mailto:charter@privatejetexecutive.com?subject=Private%20Charter%20Enquiry">Email our team</a></aside>
    </div>
</section>

<section class="section">
    <div class="container form-shell">
        <div class="form-shell__heading"><p class="eyebrow">Online enquiry</p><h2>How can we help?</h2><p>Use this form for general enquiries, feedback, partnerships or a business discussion.</p></div>
        <?php if ($formState['notice'] !== null): ?><p class="form-notice" role="status"><?= $escape($formState['notice']) ?></p><?php endif; ?>
        <?php if (isset($errors['_form'])): ?><p class="form-notice form-notice--error" role="alert"><?= $escape($errors['_form']) ?></p><?php endif; ?>
        <form class="inquiry-form" action="/contact" method="post" data-inquiry-form>
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <div class="honeypot" aria-hidden="true"><label>Company website<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label></div>
            <div class="form-grid">
                <div class="field"><label for="full_name">Full name <span aria-hidden="true">*</span></label><input id="full_name" name="full_name" autocomplete="name" maxlength="120" required value="<?= $escape($old['full_name'] ?? '') ?>"><?php if (isset($errors['full_name'])): ?><small class="field-error"><?= $escape($errors['full_name']) ?></small><?php endif; ?></div>
                <div class="field"><label for="email">Email address <span aria-hidden="true">*</span></label><input id="email" name="email" type="email" autocomplete="email" required value="<?= $escape($old['email'] ?? '') ?>"><?php if (isset($errors['email'])): ?><small class="field-error"><?= $escape($errors['email']) ?></small><?php endif; ?></div>
                <div class="field field--phone"><label for="country_code">Contact number <span aria-hidden="true">*</span></label><div class="phone-group"><select id="country_code" name="country_code" required aria-label="Country calling code"><option value="">Country code</option><?php $selectedCountry = $old['country_code'] ?? ''; require dirname(__DIR__) . '/partials/country-options.php'; ?></select><input id="phone" name="phone" type="tel" inputmode="numeric" pattern="[0-9]*" autocomplete="tel-national" placeholder="Phone number" required value="<?= $escape($old['phone'] ?? '') ?>"></div><?php if (isset($errors['country_code']) || isset($errors['phone'])): ?><small class="field-error"><?= $escape($errors['country_code'] ?? $errors['phone']) ?></small><?php endif; ?></div>
                <div class="field"><label for="topic">Topic <span aria-hidden="true">*</span></label><select id="topic" name="topic" required><option value="">Select a topic</option><?php foreach (['General Inquiry', 'Suggestion', 'Feedback', 'Complaint', 'Appreciation', 'Partnership', 'Business Inquiry', 'Other'] as $topic): ?><option value="<?= $escape($topic) ?>" <?= ($old['topic'] ?? '') === $topic ? 'selected' : '' ?>><?= $escape($topic) ?></option><?php endforeach; ?></select><?php if (isset($errors['topic'])): ?><small class="field-error"><?= $escape($errors['topic']) ?></small><?php endif; ?></div>
                <div class="field field--full"><label for="subject">Subject <span aria-hidden="true">*</span></label><input id="subject" name="subject" maxlength="160" required value="<?= $escape($old['subject'] ?? '') ?>"><?php if (isset($errors['subject'])): ?><small class="field-error"><?= $escape($errors['subject']) ?></small><?php endif; ?></div>
                <div class="field field--full"><label for="message">Message <span aria-hidden="true">*</span></label><textarea id="message" name="message" rows="6" maxlength="3000" required data-count-target="message-count"><?= $escape($old['message'] ?? '') ?></textarea><div class="field-meta"><small id="message-count">0 / 3,000</small><?php if (isset($errors['message'])): ?><small class="field-error"><?= $escape($errors['message']) ?></small><?php endif; ?></div></div>
                <div class="field field--captcha"><label for="captcha_answer">Verification: <?= $escape($captchaQuestion) ?> <span aria-hidden="true">*</span></label><input id="captcha_answer" name="captcha_answer" inputmode="numeric" required><?php if (isset($errors['captcha_answer'])): ?><small class="field-error"><?= $escape($errors['captcha_answer']) ?></small><?php endif; ?></div>
            </div>
            <div class="form-submit"><p>By submitting, you agree that we may use your details to respond to this enquiry. Please do not include sensitive payment information.</p><button class="button" type="submit">Send enquiry</button></div>
        </form>
    </div>
</section>

<section class="section section--charcoal"><div class="container centered-copy"><p class="eyebrow">Personal service</p><h2>Jakarta · Semarang · Worldwide</h2><p>A dedicated online enquiry form is being prepared. In the meantime, our team is available by email.</p></div></section>
