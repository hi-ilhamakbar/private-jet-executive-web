<?php

declare(strict_types=1);

namespace App\Invoice;

use Dompdf\Dompdf;
use Dompdf\Options;

final class InvoicePdf
{
    /** @param array<string, mixed> $invoice */
    public static function render(array $invoice): string
    {
        $projectRoot = dirname(__DIR__, 2);
        $autoload = $projectRoot . '/vendor/autoload.php';
        if (!is_file($autoload)) throw new \RuntimeException('PDF dependency is not installed.');
        require_once $autoload;

        $escape = static fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        $amount = InvoiceData::usd((int) $invoice['total_usd_cents']);
        $amountWords = InvoiceData::words((int) $invoice['total_usd_cents']);
        $generatedAt = new \DateTimeImmutable((string) $invoice['generated_at'], new \DateTimeZone('Asia/Jakarta'));
        $logoPath = $projectRoot . '/public/assets/images/logo-transparent.png';
        $logoData = is_file($logoPath) ? 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath)) : '';

        ob_start();
        require $projectRoot . '/templates/invoices/invoice.php';
        $html = (string) ob_get_clean();

        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);
        $pdf = new Dompdf($options);
        $pdf->setPaper('A4');
        $pdf->loadHtml($html, 'UTF-8');
        $pdf->render();
        return $pdf->output();
    }
}
