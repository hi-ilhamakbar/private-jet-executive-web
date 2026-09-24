<?php

declare(strict_types=1);

namespace App\Invoice;

use PDO;

final class InvoiceRepository
{
    public function __construct(private PDO $database) {}

    public function exists(string $number): bool
    {
        $statement = $this->database->prepare('SELECT 1 FROM invoices WHERE invoice_number = :number LIMIT 1');
        $statement->execute(['number' => $number]);
        return $statement->fetchColumn() !== false;
    }

    /** @param array<string, string> $values
     * @return array<string, mixed>
     */
    public function create(string $number, array $values, \DateTimeImmutable $generatedAt): array
    {
        $statement = $this->database->prepare(
            'INSERT INTO invoices (invoice_number, journey_type, route, outbound_at, return_at, aircraft_type, capacity, additional_request, total_usd_cents, generated_at) VALUES (:invoice_number, :journey_type, :route, :outbound_at, :return_at, :aircraft_type, :capacity, :additional_request, :total_usd_cents, :generated_at)'
        );
        $statement->execute([
            'invoice_number' => $number,
            'journey_type' => $values['journey_type'],
            'route' => $values['route'],
            'outbound_at' => $values['outbound_at'],
            'return_at' => $values['journey_type'] === 'return' ? $values['return_at'] : null,
            'aircraft_type' => $values['aircraft_type'],
            'capacity' => (int) $values['capacity'],
            'additional_request' => $values['additional_request'] !== '' ? $values['additional_request'] : null,
            'total_usd_cents' => (int) $values['total_cents'],
            'generated_at' => $generatedAt->format('Y-m-d H:i:s'),
        ]);
        return $this->find($number) ?? throw new \RuntimeException('Invoice record could not be read.');
    }

    /** @return array<string, mixed>|null */
    public function find(string $number): ?array
    {
        $statement = $this->database->prepare('SELECT * FROM invoices WHERE invoice_number = :number LIMIT 1');
        $statement->execute(['number' => $number]);
        $invoice = $statement->fetch();
        return is_array($invoice) ? $invoice : null;
    }

    /** @return list<array<string, mixed>> */
    public function latest(int $limit = 12): array
    {
        return $this->database->query('SELECT invoice_number, route, aircraft_type, total_usd_cents, generated_at FROM invoices ORDER BY id DESC LIMIT ' . max(1, min($limit, 50)))->fetchAll();
    }
}
