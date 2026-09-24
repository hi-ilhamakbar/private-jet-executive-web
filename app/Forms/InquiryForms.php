<?php

declare(strict_types=1);

namespace App\Forms;

final class InquiryForms
{
    private const RATE_LIMIT_WINDOW = 600;
    private const RATE_LIMIT_MAX_ATTEMPTS = 5;
    private const CAPTCHA_VERSION = 2;

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name('pje_session');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => self::isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }

    public static function csrfToken(): string
    {
        self::startSession();

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['csrf_token'];
    }

    /** @return array{question: string} */
    public static function captcha(string $form): array
    {
        self::startSession();
        $challenge = $_SESSION['captcha'][$form] ?? null;

        if (!is_array($challenge) || ($challenge['version'] ?? null) !== self::CAPTCHA_VERSION) {
            $challenge = self::newChallenge();
            $_SESSION['captcha'][$form] = $challenge;
        }

        return ['question' => $challenge['question']];
    }

    /** @param array<string, mixed> $input
     *  @return array{errors: array<string, string>, old: array<string, string>, notice: string|null, submitted: bool, ready: bool}
     */
    public static function process(string $form, array $input): array
    {
        self::startSession();
        $old = self::oldValues($input);
        $result = ['errors' => [], 'old' => $old, 'notice' => null, 'submitted' => true, 'ready' => false];

        if (!self::withinRateLimit($form)) {
            $result['errors']['_form'] = 'Please wait a few minutes before trying again.';
            return $result;
        }

        if (trim((string) ($input['company_website'] ?? '')) !== '') {
            // Do not confirm that anti-automation protection was triggered.
            $result['notice'] = 'Thank you. Please contact charter@privatejetexecutive.com for assistance.';
            return $result;
        }

        $token = (string) ($input['csrf_token'] ?? '');
        if (!hash_equals(self::csrfToken(), $token)) {
            $result['errors']['_form'] = 'Your session has expired. Please refresh the page and try again.';
            return $result;
        }

        $result['errors'] += self::validateCaptcha($form, (string) ($input['captcha_answer'] ?? ''));
        $result['errors'] += $form === 'charter'
            ? self::validateCharter($old)
            : self::validateContact($old);

        if ($result['errors'] !== []) {
            if (isset($result['errors']['captcha_answer'])) {
                self::rotateCaptcha($form);
            }

            return $result;
        }

        self::rotateCaptcha($form);
        $result['ready'] = true;
        return $result;
    }

    /** @param array<string, string> $data */
    public static function isDuplicateSubmission(string $form, array $data): bool
    {
        self::startSession();
        $key = hash('sha256', $form . '|' . json_encode($data));
        $timestamp = $_SESSION['recent_submissions'][$key] ?? 0;

        return is_int($timestamp) && $timestamp > time() - 600;
    }

    /** @param array<string, string> $data */
    public static function rememberSubmission(string $form, array $data): void
    {
        $_SESSION['recent_submissions'][hash('sha256', $form . '|' . json_encode($data))] = time();
    }

    public static function flashSuccess(string $form, string $reference): void
    {
        $_SESSION['form_flash'][$form] = $reference;
    }

    public static function consumeFlash(string $form): ?string
    {
        $reference = $_SESSION['form_flash'][$form] ?? null;
        unset($_SESSION['form_flash'][$form]);
        return is_string($reference) ? $reference : null;
    }

    /** @return array<string, string> */
    private static function validateCharter(array $values): array
    {
        $errors = self::validateIdentity($values);
        self::required($values, $errors, 'journey_type', 'Please choose one way or return.');
        self::required($values, $errors, 'departure', 'Please enter a departure location.');
        self::required($values, $errors, 'arrival', 'Please enter an arrival location.');
        self::required($values, $errors, 'departure_date', 'Please select a departure date.');
        self::required($values, $errors, 'departure_time', 'Please select an estimated departure time.');
        self::required($values, $errors, 'adults', 'Please enter the number of adult passengers.');

        self::validateDate($values, $errors, 'departure_date', 'Departure date must be today or a future date.');
        self::validateFiveMinuteTime($values, $errors, 'departure_time');
        self::validatePassengerCount($values, $errors, 'adults', 1, 'At least one adult passenger is required.');
        self::validatePassengerCount($values, $errors, 'children', 0, 'Please enter a valid number of child passengers.');
        self::validatePassengerCount($values, $errors, 'infants', 0, 'Please enter a valid number of infant passengers.');

        if (($values['journey_type'] ?? '') === 'return') {
            self::required($values, $errors, 'return_date', 'Please select a return date.');
            self::required($values, $errors, 'return_time', 'Please select an estimated return time.');

            if (($values['departure_date'] ?? '') !== '' && ($values['return_date'] ?? '') !== ''
                && $values['return_date'] < $values['departure_date']) {
                $errors['return_date'] = 'Return date cannot be earlier than departure date.';
            }
            self::validateDate($values, $errors, 'return_date', 'Return date must be today or a future date.');
            self::validateFiveMinuteTime($values, $errors, 'return_time');
        }

        if (mb_strlen($values['notes'] ?? '') > 500) {
            $errors['notes'] = 'Request notes must be 500 characters or fewer.';
        }

        return $errors;
    }

    /** @return array<string, string> */
    private static function validateContact(array $values): array
    {
        $errors = self::validateIdentity($values);
        self::required($values, $errors, 'topic', 'Please choose a topic.');
        self::required($values, $errors, 'subject', 'Please enter a subject.');
        self::required($values, $errors, 'message', 'Please enter your message.');

        if (mb_strlen($values['subject'] ?? '') > 160) {
            $errors['subject'] = 'Subject must be 160 characters or fewer.';
        }
        if (mb_strlen($values['message'] ?? '') > 3000) {
            $errors['message'] = 'Message must be 3,000 characters or fewer.';
        }

        return $errors;
    }

    /** @return array<string, string> */
    private static function validateIdentity(array $values): array
    {
        $errors = [];
        self::required($values, $errors, 'full_name', 'Please enter your full name.');
        self::required($values, $errors, 'email', 'Please enter your email address.');
        self::required($values, $errors, 'country_code', 'Please select a country calling code.');
        self::required($values, $errors, 'phone', 'Please enter your contact number.');

        if (mb_strlen($values['full_name'] ?? '') > 120) {
            $errors['full_name'] = 'Full name must be 120 characters or fewer.';
        }
        if (($values['email'] ?? '') !== '' && filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Please enter a valid email address.';
        }
        if (($values['country_code'] ?? '') !== '' && !preg_match('/^\+[1-9][0-9]{0,3}$/', $values['country_code'])) {
            $errors['country_code'] = 'Please select a valid country calling code.';
        }
        if (($values['phone'] ?? '') !== '' && !preg_match('/^[0-9]{5,15}$/', $values['phone'])) {
            $errors['phone'] = 'Please enter 5 to 15 digits only.';
        }

        return $errors;
    }

    /** @param array<string, string> $errors */
    private static function required(array $values, array &$errors, string $field, string $message): void
    {
        if (trim($values[$field] ?? '') === '') {
            $errors[$field] = $message;
        }
    }

    /** @param array<string, string> $errors */
    private static function validateDate(array $values, array &$errors, string $field, string $message): void
    {
        $value = $values[$field] ?? '';
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value, new \DateTimeZone('Asia/Jakarta'));
        $today = new \DateTimeImmutable('today', new \DateTimeZone('Asia/Jakarta'));

        if ($value !== '' && ($date === false || $date->format('Y-m-d') !== $value || $date < $today)) {
            $errors[$field] = $message;
        }
    }

    /** @param array<string, string> $errors */
    private static function validateFiveMinuteTime(array $values, array &$errors, string $field): void
    {
        $value = $values[$field] ?? '';

        if ($value === '') {
            return;
        }

        $parts = explode(':', $value);
        if (count($parts) !== 2 || !ctype_digit($parts[0]) || !ctype_digit($parts[1]) || (int) $parts[0] > 23 || (int) $parts[1] > 59 || (int) $parts[1] % 5 !== 0) {
            $errors[$field] = 'Please choose a time in five-minute intervals.';
        }
    }

    /** @param array<string, string> $errors */
    private static function validatePassengerCount(array $values, array &$errors, string $field, int $minimum, string $message): void
    {
        $value = $values[$field] ?? '';

        if ($value !== '' && (!ctype_digit($value) || (int) $value < $minimum || (int) $value > 99)) {
            $errors[$field] = $message;
        }
    }

    /** @return array<string, string> */
    private static function validateCaptcha(string $form, string $answer): array
    {
        $challenge = $_SESSION['captcha'][$form] ?? null;

        if (!is_array($challenge) || ($challenge['version'] ?? null) !== self::CAPTCHA_VERSION || !isset($challenge['answer']) || !preg_match('/^[0-9]{1,3}$/', trim($answer))
            || !hash_equals((string) $challenge['answer'], trim($answer))) {
            return ['captcha_answer' => 'Please solve the verification question.'];
        }

        return [];
    }

    /** @param array<string, mixed> $input
     *  @return array<string, string>
     */
    private static function oldValues(array $input): array
    {
        $allowed = ['full_name', 'email', 'country_code', 'phone', 'journey_type', 'departure', 'arrival', 'departure_date', 'departure_time', 'return_date', 'return_time', 'adults', 'children', 'infants', 'notes', 'topic', 'subject', 'message'];
        $values = [];

        foreach ($allowed as $field) {
            $value = trim((string) ($input[$field] ?? ''));
            $values[$field] = str_replace(["\r\n", "\r"], "\n", $value);
        }

        $values['phone'] = preg_replace('/\D+/', '', $values['phone']) ?? '';
        return $values;
    }

    private static function rotateCaptcha(string $form): void
    {
        $_SESSION['captcha'][$form] = self::newChallenge();
    }

    /** @return array{version: int, question: string, answer: int} */
    private static function newChallenge(): array
    {
        // Keep the challenge effortless for genuine visitors: one-digit arithmetic only.
        $left = random_int(1, 9);
        $addition = random_int(0, 1) === 1;

        // For subtraction, choose the second number from the first number's range
        // so the expected result can never be negative.
        $right = $addition ? random_int(1, 9) : random_int(1, $left);

        return [
            'version' => self::CAPTCHA_VERSION,
            'question' => $left . ($addition ? ' + ' : ' − ') . $right . ' = ?',
            'answer' => $addition ? $left + $right : $left - $right,
        ];
    }

    private static function withinRateLimit(string $form): bool
    {
        $now = time();
        $attempts = $_SESSION['form_attempts'][$form] ?? [];
        $attempts = array_values(array_filter($attempts, static fn (mixed $timestamp): bool => is_int($timestamp) && $timestamp > $now - self::RATE_LIMIT_WINDOW));

        if (count($attempts) >= self::RATE_LIMIT_MAX_ATTEMPTS) {
            $_SESSION['form_attempts'][$form] = $attempts;
            return false;
        }

        $attempts[] = $now;
        $_SESSION['form_attempts'][$form] = $attempts;
        return true;
    }

    private static function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (string) ($_SERVER['SERVER_PORT'] ?? '') === '443';
    }
}
