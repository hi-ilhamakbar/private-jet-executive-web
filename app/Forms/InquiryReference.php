<?php

declare(strict_types=1);

namespace App\Forms;

final class InquiryReference
{
    public static function generate(string $form): string
    {
        $prefix = $form === 'charter' ? 'CH' : 'CT';
        $date = new \DateTimeImmutable('now', new \DateTimeZone('Asia/Jakarta'));

        return sprintf('%s-%s-%05d', $prefix, $date->format('ymd'), random_int(0, 99999));
    }
}
