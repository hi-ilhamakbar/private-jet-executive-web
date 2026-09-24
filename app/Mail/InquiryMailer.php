<?php

declare(strict_types=1);

namespace App\Mail;

use App\Core\Environment;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

final class InquiryMailer
{
    /** @param array<string, string> $data */
    public static function send(string $form, array $data, string $reference): void
    {
        self::loadMailerLibrary();
        $mail = self::mailer();
        $isCharter = $form === 'charter';
        $name = $data['full_name'];
        $mail->addAddress($data['email'], $name);
        $mail->Subject = ($isCharter ? 'Your Private Charter Inquiry' : 'Your Message to Private Jet Executive') . ' · ' . $reference;
        $mail->isHTML(true);
        $mail->Body = self::renderTemplate($isCharter, $data, $reference);
        $mail->AltBody = self::plainText($isCharter, $data, $reference);
        $mail->send();
    }

    private static function loadMailerLibrary(): void
    {
        $vendor = dirname(__DIR__, 2) . '/vendor';
        $composerAutoload = $vendor . '/autoload.php';

        if (is_file($composerAutoload)) {
            require_once $composerAutoload;
            return;
        }

        // Supports a direct PHPMailer source upload when Composer is unavailable on shared hosting.
        $source = $vendor . '/PHPMailer-master/src';
        $required = [$source . '/Exception.php', $source . '/SMTP.php', $source . '/PHPMailer.php'];
        foreach ($required as $file) {
            if (!is_file($file)) {
                throw new \RuntimeException('Mail dependency is not installed.');
            }
        }

        foreach ($required as $file) {
            require_once $file;
        }
    }

    private static function mailer(): PHPMailer
    {
        $required = ['SMTP_HOST', 'SMTP_USERNAME', 'SMTP_PASSWORD', 'MAIL_FROM'];
        foreach ($required as $key) {
            if (Environment::get($key) === '') {
                throw new \RuntimeException('Mail configuration is incomplete.');
            }
        }

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = Environment::get('SMTP_HOST');
        $mail->Port = (int) Environment::get('SMTP_PORT', '465');
        $mail->SMTPAuth = true;
        $mail->Username = Environment::get('SMTP_USERNAME');
        $mail->Password = Environment::get('SMTP_PASSWORD');
        $mail->SMTPSecure = Environment::get('SMTP_ENCRYPTION', 'ssl') === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->setFrom(Environment::get('MAIL_FROM'), Environment::get('MAIL_FROM_NAME', 'Private Jet Executive'));
        $mail->addReplyTo(Environment::get('MAIL_REPLY_TO', 'charter@privatejetexecutive.com'));

        foreach (['MAIL_BCC_1', 'MAIL_BCC_2'] as $key) {
            $address = Environment::get($key);
            if ($address !== '' && filter_var($address, FILTER_VALIDATE_EMAIL) !== false) {
                $mail->addBCC($address);
            }
        }

        return $mail;
    }

    /** @param array<string, string> $data */
    private static function renderTemplate(bool $isCharter, array $data, string $reference): string
    {
        $details = self::details($isCharter, $data, $reference);
        $heading = $isCharter ? 'Your Private Charter Inquiry' : 'Thank You for Contacting Us';
        $intro = $isCharter
            ? 'Dear ' . $data['full_name'] . ', thank you for contacting Private Jet Executive. We have received your charter inquiry. A member of our team will review your requirements and contact you regarding the next steps.'
            : 'Dear ' . $data['full_name'] . ', thank you for contacting Private Jet Executive. We have received your message and a member of our team will respond as soon as possible.';
        $disclaimer = $isCharter
            ? 'Submission of an inquiry is not a booking, aircraft, price, or payment confirmation. All arrangements remain subject to availability, quotation, operational requirements, and final confirmation.'
            : 'This message confirms receipt of your enquiry. Please do not send payment details by email.';

        ob_start();
        require dirname(__DIR__, 2) . '/templates/emails/inquiry.php';
        return (string) ob_get_clean();
    }

    /** @param array<string, string> $data
     *  @return array<string, string>
     */
    private static function details(bool $isCharter, array $data, string $reference): array
    {
        $base = [
            'Reference' => $reference,
            'Full Name' => $data['full_name'],
            'Contact Number' => $data['country_code'] . $data['phone'],
            'Email Address' => $data['email'],
        ];

        if (!$isCharter) {
            return $base + ['Topic' => $data['topic'], 'Subject' => $data['subject'], 'Message' => $data['message'], 'Submitted At' => self::submittedAt()];
        }

        return $base + [
            'Journey Type' => $data['journey_type'] === 'return' ? 'Return' : 'One Way',
            'Departure' => $data['departure'], 'Arrival' => $data['arrival'], 'Departure Date' => $data['departure_date'], 'Estimated Departure Time' => $data['departure_time'],
            'Return Date' => $data['journey_type'] === 'return' ? $data['return_date'] : 'Not applicable', 'Estimated Return Time' => $data['journey_type'] === 'return' ? $data['return_time'] : 'Not applicable',
            'Passengers' => 'Adults: ' . $data['adults'] . '; Children: ' . $data['children'] . '; Infants: ' . $data['infants'], 'Request Notes' => $data['notes'] !== '' ? $data['notes'] : 'None', 'Submitted At' => self::submittedAt(),
        ];
    }

    /** @param array<string, string> $data */
    private static function plainText(bool $isCharter, array $data, string $reference): string
    {
        $lines = ['Private Jet Executive', 'Reference: ' . $reference, ''];
        foreach (self::details($isCharter, $data, $reference) as $label => $value) {
            $lines[] = $label . ': ' . $value;
        }
        return implode("\n", $lines);
    }

    private static function submittedAt(): string
    {
        return (new \DateTimeImmutable('now', new \DateTimeZone('Asia/Jakarta')))->format('l, d F Y; H:i') . ' UTC+7';
    }
}
