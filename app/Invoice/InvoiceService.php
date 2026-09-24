<?php

declare(strict_types=1);

namespace App\Invoice;

final class InvoiceService
{
    public function __construct(private InvoiceRepository $repository) {}

    /** @param array<string, string> $values
     * @return array<string, mixed>
     */
    public function create(array $values): array
    {
        $generatedAt = new \DateTimeImmutable('now', new \DateTimeZone('Asia/Jakarta'));
        $dueAt = self::dueAt($generatedAt);
        $datePrefix = $generatedAt->format('ymd');
        $epochSeed = time() % 100000;

        for ($attempt = 0; $attempt < 100; $attempt++) {
            $suffix = str_pad((string) (($epochSeed + $attempt) % 100000), 5, '0', STR_PAD_LEFT);
            $number = '#INV/' . $datePrefix . '/' . $suffix;
            if ($this->repository->exists($number)) continue;

            try {
                return $this->repository->create($number, $values, $generatedAt, $dueAt);
            } catch (\PDOException $exception) {
                if ($exception->getCode() !== '23000') throw $exception;
            }
        }

        throw new \RuntimeException('Unable to allocate a unique invoice number.');
    }

    public static function dueAt(\DateTimeImmutable $generatedAt): \DateTimeImmutable
    {
        return $generatedAt->format('N') === '5'
            ? $generatedAt->modify('+3 days')
            : $generatedAt->modify('+1 day');
    }
}
