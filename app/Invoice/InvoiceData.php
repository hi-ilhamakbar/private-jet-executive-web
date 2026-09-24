<?php

declare(strict_types=1);

namespace App\Invoice;

final class InvoiceData
{
    /** @param array<string, mixed> $input
     * @return array{values: array<string, string>, errors: array<string, string>}
     */
    public static function validate(array $input): array
    {
        $fields = ['journey_type', 'route', 'outbound_at', 'return_at', 'aircraft_type', 'capacity', 'additional_request', 'total_amount'];
        $values = [];
        foreach ($fields as $field) {
            $values[$field] = trim((string) ($input[$field] ?? ''));
        }

        $errors = [];
        if (!in_array($values['journey_type'], ['one_way', 'return'], true)) $errors['journey_type'] = 'Select a journey type.';
        if ($values['route'] === '' || mb_strlen($values['route']) > 180) $errors['route'] = 'Enter a route of up to 180 characters.';
        if ($values['aircraft_type'] === '' || mb_strlen($values['aircraft_type']) > 100) $errors['aircraft_type'] = 'Enter the aircraft type.';
        if (!ctype_digit($values['capacity']) || (int) $values['capacity'] < 1 || (int) $values['capacity'] > 999) $errors['capacity'] = 'Enter a valid aircraft capacity.';
        if (mb_strlen($values['additional_request']) > 1500) $errors['additional_request'] = 'Additional requests must be 1,500 characters or fewer.';

        $outbound = self::date($values['outbound_at']);
        if ($outbound === null) $errors['outbound_at'] = 'Enter the outbound flight date and time.';
        $return = self::date($values['return_at']);
        if ($values['journey_type'] === 'return' && $return === null) $errors['return_at'] = 'Enter the return flight date and time.';
        if ($outbound !== null && $return !== null && $return < $outbound) $errors['return_at'] = 'The return flight must be after the outbound flight.';

        $cents = self::toCents($values['total_amount']);
        if ($cents === null || $cents <= 0) $errors['total_amount'] = 'Enter a valid total amount in USD.';
        $values['total_cents'] = (string) ($cents ?? 0);
        $values['outbound_at'] = $outbound?->format('Y-m-d H:i:s') ?? '';
        $values['return_at'] = $return?->format('Y-m-d H:i:s') ?? '';

        return ['values' => $values, 'errors' => $errors];
    }

    public static function usd(int $cents): string
    {
        return 'USD ' . number_format($cents / 100, 2, '.', ',');
    }

    public static function words(int $cents): string
    {
        $dollars = intdiv($cents, 100);
        $remaining = $cents % 100;
        $words = self::numberWords($dollars) . ' US Dollars';
        if ($remaining > 0) $words .= ' and ' . self::numberWords($remaining) . ' Cents';
        return $words . ' Only';
    }

    private static function date(string $value): ?\DateTimeImmutable
    {
        if ($value === '') return null;
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i', $value, new \DateTimeZone('Asia/Jakarta'));
        return $date !== false && $date->format('Y-m-d\\TH:i') === $value ? $date : null;
    }

    private static function toCents(string $value): ?int
    {
        $normalised = str_replace([',', ' '], '', $value);
        if (!preg_match('/^\d{1,12}(?:\.\d{1,2})?$/', $normalised)) return null;
        [$whole, $fraction] = array_pad(explode('.', $normalised, 2), 2, '');
        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }

    private static function numberWords(int $number): string
    {
        $small = [0 => 'Zero', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'];
        $tens = [2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty', 6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'];
        if ($number < 20) return $small[$number];
        if ($number < 100) return $tens[intdiv($number, 10)] . ($number % 10 ? '-' . $small[$number % 10] : '');
        if ($number < 1000) return $small[intdiv($number, 100)] . ' Hundred' . ($number % 100 ? ' ' . self::numberWords($number % 100) : '');
        foreach ([1000000000 => 'Billion', 1000000 => 'Million', 1000 => 'Thousand'] as $unit => $label) {
            if ($number >= $unit) return self::numberWords(intdiv($number, $unit)) . ' ' . $label . ($number % $unit ? ' ' . self::numberWords($number % $unit) : '');
        }
        return 'Zero';
    }
}
