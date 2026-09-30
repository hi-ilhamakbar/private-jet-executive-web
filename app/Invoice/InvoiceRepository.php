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
    public function create(string $number, array $values, \DateTimeImmutable $generatedAt, \DateTimeImmutable $dueAt): array
    {
        $statement = $this->database->prepare(
            'INSERT INTO invoices (invoice_number, invoice_recipient, recipient_email, recipient_country_code, recipient_phone, journey_type, route, outbound_at, return_at, aircraft_type, capacity, additional_request, total_usd_cents, generated_at, due_at) VALUES (:invoice_number, :invoice_recipient, :recipient_email, :recipient_country_code, :recipient_phone, :journey_type, :route, :outbound_at, :return_at, :aircraft_type, :capacity, :additional_request, :total_usd_cents, :generated_at, :due_at)'
        );
        $statement->execute([
            'invoice_number' => $number,
            'invoice_recipient' => $values['invoice_recipient'],
            'recipient_email' => $values['recipient_email'],
            'recipient_country_code' => $values['recipient_country_code'] !== '' ? $values['recipient_country_code'] : null,
            'recipient_phone' => $values['recipient_phone'] !== '' ? $values['recipient_phone'] : null,
            'journey_type' => $values['journey_type'],
            'route' => $values['route'],
            'outbound_at' => $values['outbound_at'],
            'return_at' => $values['journey_type'] === 'return' ? $values['return_at'] : null,
            'aircraft_type' => $values['aircraft_type'],
            'capacity' => (int) $values['capacity'],
            'additional_request' => $values['additional_request'] !== '' ? $values['additional_request'] : null,
            'total_usd_cents' => (int) $values['total_cents'],
            'generated_at' => $generatedAt->format('Y-m-d H:i:s'),
            'due_at' => $dueAt->format('Y-m-d H:i:s'),
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
    public function search(string $sort, string $direction, ?string $dateField, ?string $from, ?string $to, int $limit = 100): array
    {
        $sortColumn = $sort === 'due_at' ? 'due_at' : 'generated_at';
        $sortDirection = $direction === 'asc' ? 'ASC' : 'DESC';
        $filterColumn = $dateField === 'due_at' ? 'due_at' : 'generated_at';
        $sql = 'SELECT invoice_number, invoice_recipient, recipient_email, route, aircraft_type, total_usd_cents, due_at, generated_at FROM invoices';
        $conditions = [];
        $parameters = [];
        if ($from !== null) { $conditions[] = $filterColumn . ' >= :from'; $parameters['from'] = $from . ' 00:00:00'; }
        if ($to !== null) { $conditions[] = $filterColumn . ' <= :to'; $parameters['to'] = $to . ' 23:59:59'; }
        if ($conditions !== []) $sql .= ' WHERE ' . implode(' AND ', $conditions);
        $sql .= ' ORDER BY ' . $sortColumn . ' ' . $sortDirection . ', id ' . $sortDirection . ' LIMIT ' . max(1, min($limit, 200));
        $statement = $this->database->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll();
    }
}
