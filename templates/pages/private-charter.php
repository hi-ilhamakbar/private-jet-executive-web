<?php
$old = $formState['old'];
$errors = $formState['errors'];
$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$today = date('Y-m-d');
?>
<section class="page-hero">
    <div class="container page-hero__content">
        <p class="eyebrow">Private charter</p>
        <h1>Your time, your route, your priorities.</h1>
        <p class="lead">For journeys where schedule, privacy and attention to detail matter, we arrange charter solutions around the way you travel.</p>
        <a class="button" href="/contact">Speak with our team</a>
    </div>
</section>

<section class="section section--pearl">
    <div class="container grid-two">
        <div><p class="eyebrow eyebrow--dark">A considered alternative</p><h2>Travel arranged around you.</h2></div>
        <div class="prose"><p>Private charter gives you greater control over timing, routing and the experience on the ground. Whether you are travelling for a family occasion, an important meeting or a discreet personal commitment, each request starts with a conversation about what matters most.</p><p>From our base in Indonesia, we coordinate charter requirements for journeys within the region and onward to destinations worldwide. Every itinerary remains subject to aircraft availability, applicable operational requirements and final confirmation.</p></div>
    </div>
</section>

<section class="section">
    <div class="container"><p class="eyebrow">What we consider</p><h2>Every detail has a purpose.</h2><div class="feature-grid">
        <article class="feature-card"><h3>Route and timing</h3><p>Departure points, preferred schedules, airport access and the practicalities of your onward journey.</p></article>
        <article class="feature-card"><h3>Aircraft suitability</h3><p>Options considered against passenger numbers, luggage, range, cabin needs and the nature of the journey.</p></article>
        <article class="feature-card"><h3>Ground coordination</h3><p>Clear communication around the journey, with the discretion expected of private aviation.</p></article>
    </div></div>
</section>

<section class="section section--charcoal"><div class="container centered-copy"><p class="eyebrow">Start a conversation</p><h2>Tell us where you need to be.</h2><p>Share the essentials of your proposed journey and our team will review the available options with you.</p></div></section>

<section class="section section--pearl">
    <div class="container form-shell">
        <div class="form-shell__heading"><p class="eyebrow eyebrow--dark">Request a charter</p><h2>Plan your journey.</h2><p>Share the essential details below. This is an enquiry, not an aircraft or price confirmation.</p></div>
        <?php if ($formState['notice'] !== null): ?><p class="form-notice" role="status"><?= $escape($formState['notice']) ?></p><?php endif; ?>
        <?php if (isset($errors['_form'])): ?><p class="form-notice form-notice--error" role="alert"><?= $escape($errors['_form']) ?></p><?php endif; ?>
        <form class="inquiry-form inquiry-form--light" action="/private-charter" method="post" data-inquiry-form>
            <input type="hidden" name="csrf_token" value="<?= $escape($csrfToken) ?>">
            <div class="honeypot" aria-hidden="true"><label>Company website<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label></div>
            <fieldset class="journey-type"><legend>Journey type <span aria-hidden="true">*</span></legend><label><input type="radio" name="journey_type" value="one_way" <?= ($old['journey_type'] ?? 'one_way') !== 'return' ? 'checked' : '' ?> data-journey-type> One Way</label><label><input type="radio" name="journey_type" value="return" <?= ($old['journey_type'] ?? '') === 'return' ? 'checked' : '' ?> data-journey-type> Return</label><?php if (isset($errors['journey_type'])): ?><small class="field-error"><?= $escape($errors['journey_type']) ?></small><?php endif; ?></fieldset>
            <div class="form-grid">
                <div class="field airport-field"><label for="departure">Departure <span aria-hidden="true">*</span></label><input id="departure" name="departure" autocomplete="off" placeholder="Search city, airport, or IATA code" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="departure-results" data-airport-search required value="<?= $escape($old['departure'] ?? '') ?>"><div id="departure-results" class="airport-results" role="listbox" hidden></div><small class="field-hint" data-airport-status aria-live="polite"></small><?php if (isset($errors['departure'])): ?><small class="field-error"><?= $escape($errors['departure']) ?></small><?php endif; ?></div>
                <div class="field airport-field"><label for="arrival">Arrival <span aria-hidden="true">*</span></label><input id="arrival" name="arrival" autocomplete="off" placeholder="Search city, airport, or IATA code" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-controls="arrival-results" data-airport-search required value="<?= $escape($old['arrival'] ?? '') ?>"><div id="arrival-results" class="airport-results" role="listbox" hidden></div><small class="field-hint" data-airport-status aria-live="polite"></small><?php if (isset($errors['arrival'])): ?><small class="field-error"><?= $escape($errors['arrival']) ?></small><?php endif; ?></div>
                <div class="field"><label for="departure_date">Departure date <span aria-hidden="true">*</span></label><input id="departure_date" name="departure_date" type="date" min="<?= $today ?>" required value="<?= $escape($old['departure_date'] ?? '') ?>"><?php if (isset($errors['departure_date'])): ?><small class="field-error"><?= $escape($errors['departure_date']) ?></small><?php endif; ?></div>
                <div class="field"><label for="departure_time">Estimated departure time <span aria-hidden="true">*</span></label><select id="departure_time" name="departure_time" required><option value="">Select a time</option><?php $selectedTime = $old['departure_time'] ?? ''; require dirname(__DIR__) . '/partials/time-options.php'; ?></select><?php if (isset($errors['departure_time'])): ?><small class="field-error"><?= $escape($errors['departure_time']) ?></small><?php endif; ?></div>
                <div class="field <?= ($old['journey_type'] ?? '') !== 'return' ? 'field--disabled' : '' ?>" data-return-field aria-disabled="<?= ($old['journey_type'] ?? '') !== 'return' ? 'true' : 'false' ?>"><label for="return_date">Return date <span aria-hidden="true">*</span></label><input id="return_date" name="return_date" type="date" min="<?= $old['departure_date'] ?? $today ?>" value="<?= $escape($old['return_date'] ?? '') ?>" <?= ($old['journey_type'] ?? '') !== 'return' ? 'disabled' : '' ?>><?php if (isset($errors['return_date'])): ?><small class="field-error"><?= $escape($errors['return_date']) ?></small><?php endif; ?></div>
                <div class="field <?= ($old['journey_type'] ?? '') !== 'return' ? 'field--disabled' : '' ?>" data-return-field aria-disabled="<?= ($old['journey_type'] ?? '') !== 'return' ? 'true' : 'false' ?>"><label for="return_time">Estimated return time <span aria-hidden="true">*</span></label><select id="return_time" name="return_time" <?= ($old['journey_type'] ?? '') !== 'return' ? 'disabled' : '' ?>><option value="">Select a time</option><?php $selectedTime = $old['return_time'] ?? ''; require dirname(__DIR__) . '/partials/time-options.php'; ?></select><?php if (isset($errors['return_time'])): ?><small class="field-error"><?= $escape($errors['return_time']) ?></small><?php endif; ?></div>
                <div class="field field--full passenger-field"><span class="field-label">Passengers <span aria-hidden="true">*</span></span><div class="passenger-grid"><label for="adults">Adults <small>12+ years</small><input id="adults" name="adults" type="number" min="1" max="99" step="1" inputmode="numeric" required value="<?= $escape($old['adults'] ?? '1') ?>"></label><label for="children">Children <small>2–12 years</small><input id="children" name="children" type="number" min="0" max="99" step="1" inputmode="numeric" value="<?= $escape($old['children'] ?? '0') ?>"></label><label for="infants">Infants <small>Under 2 years</small><input id="infants" name="infants" type="number" min="0" max="99" step="1" inputmode="numeric" value="<?= $escape($old['infants'] ?? '0') ?>"></label></div><?php if (isset($errors['adults']) || isset($errors['children']) || isset($errors['infants'])): ?><small class="field-error"><?= $escape($errors['adults'] ?? $errors['children'] ?? $errors['infants']) ?></small><?php endif; ?></div>
                <div class="field"><label for="charter_full_name">Full name <span aria-hidden="true">*</span></label><input id="charter_full_name" name="full_name" autocomplete="name" maxlength="120" required value="<?= $escape($old['full_name'] ?? '') ?>"><?php if (isset($errors['full_name'])): ?><small class="field-error"><?= $escape($errors['full_name']) ?></small><?php endif; ?></div>
                <div class="field"><label for="charter_email">Email address <span aria-hidden="true">*</span></label><input id="charter_email" name="email" type="email" autocomplete="email" required value="<?= $escape($old['email'] ?? '') ?>"><?php if (isset($errors['email'])): ?><small class="field-error"><?= $escape($errors['email']) ?></small><?php endif; ?></div>
                <div class="field field--phone"><label for="charter_country_code">Contact number <span aria-hidden="true">*</span></label><div class="phone-group"><select id="charter_country_code" name="country_code" required aria-label="Country calling code" data-country-picker><option value="">Country code</option><?php $selectedCountry = $old['country_code'] ?? ''; require dirname(__DIR__) . '/partials/country-options.php'; ?></select><input id="charter_phone" name="phone" type="tel" inputmode="numeric" pattern="[0-9]*" autocomplete="tel-national" placeholder="Phone number" required value="<?= $escape($old['phone'] ?? '') ?>"></div><?php if (isset($errors['country_code']) || isset($errors['phone'])): ?><small class="field-error"><?= $escape($errors['country_code'] ?? $errors['phone']) ?></small><?php endif; ?></div>
                <div class="field field--full"><label for="notes">Request notes <span class="optional">Optional</span></label><textarea id="notes" name="notes" rows="5" maxlength="500" data-count-target="notes-count" placeholder="Passenger numbers, luggage, timing or other requirements."><?= $escape($old['notes'] ?? '') ?></textarea><div class="field-meta"><small id="notes-count">0 / 500</small><?php if (isset($errors['notes'])): ?><small class="field-error"><?= $escape($errors['notes']) ?></small><?php endif; ?></div></div>
                <div class="field field--captcha"><label for="charter_captcha_answer">Verification: <?= $escape($captchaQuestion) ?> <span aria-hidden="true">*</span></label><input id="charter_captcha_answer" name="captcha_answer" inputmode="numeric" required><?php if (isset($errors['captcha_answer'])): ?><small class="field-error"><?= $escape($errors['captcha_answer']) ?></small><?php endif; ?></div>
            </div>
            <div class="form-submit"><p>By submitting, you agree that we may use your details to respond to this enquiry. Do not include payment information. A charter request is not a booking confirmation.</p><button class="button" type="submit">Request a charter</button></div>
        </form>
    </div>
</section>
