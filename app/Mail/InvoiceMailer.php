<?php

declare(strict_types=1);

namespace App\Mail;

use App\Core\Environment;
use App\Invoice\InvoiceData;
use PHPMailer\PHPMailer\PHPMailer;

final class InvoiceMailer
{
    /** @param array<string, mixed> $invoice */
    public static function send(array $invoice, string $pdf): void
    {
        $autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
        if (!is_file($autoload)) throw new \RuntimeException('Mail dependency is not installed.');
        require_once $autoload;

        foreach (['SMTP_HOST', 'SMTP_USERNAME', 'SMTP_PASSWORD'] as $key) {
            if (Environment::get($key) === '') throw new \RuntimeException('Mail configuration is incomplete.');
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
        $mail->setFrom(Environment::get('MAIL_FROM', 'noreply@privatejetexecutive.com'), Environment::get('MAIL_FROM_NAME', 'Private Jet Executive'));
        $mail->addReplyTo(Environment::get('MAIL_REPLY_TO', 'charter@privatejetexecutive.com'));
        $mail->addAddress((string) $invoice['recipient_email'], (string) $invoice['invoice_recipient']);
        foreach (['MAIL_BCC_1', 'MAIL_BCC_2'] as $key) {
            $address = Environment::get($key);
            if ($address !== '' && filter_var($address, FILTER_VALIDATE_EMAIL) !== false) $mail->addBCC($address);
        }

        $number = (string) $invoice['invoice_number'];
        $safeName = str_replace(['#', '/'], ['', '-'], $number) . '.pdf';
        $mail->Subject = 'Private Jet Executive Invoice ' . $number;
        $mail->isHTML(true);
        $mail->Body = self::renderBody($invoice);
        $mail->AltBody = self::plainBody($invoice);
        $mail->addStringAttachment($pdf, $safeName, PHPMailer::ENCODING_BASE64, 'application/pdf');
        $mail->send();
    }

    /** @param array<string, mixed> $invoice */
    private static function renderBody(array $invoice): string
    {
        $projectRoot = dirname(__DIR__, 2);
        $escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        $amount = InvoiceData::usd((int) $invoice['total_usd_cents']);
        $dueAt = new \DateTimeImmutable((string) $invoice['due_at'], new \DateTimeZone('Asia/Jakarta'));
        ob_start();
        require $projectRoot . '/templates/emails/invoice.php';
        return (string) ob_get_clean();
    }

    /** @param array<string, mixed> $invoice */
    private static function plainBody(array $invoice): string
    {
        $dueAt = new \DateTimeImmutable((string) $invoice['due_at'], new \DateTimeZone('Asia/Jakarta'));
        return "Dear " . (string) $invoice['invoice_recipient'] . ",\n\nPlease find your Private Jet Executive invoice attached.\n\nInvoice: " . (string) $invoice['invoice_number'] . "\nRoute: " . (string) $invoice['route'] . "\nTotal: " . InvoiceData::usd((int) $invoice['total_usd_cents']) . "\nPayment due: " . $dueAt->format('l, d F Y; H:i') . " WIB\n\nFor payment questions, please reply to charter@privatejetexecutive.com.\n\nKind regards,\nPrivate Jet Executive";
    }
}
