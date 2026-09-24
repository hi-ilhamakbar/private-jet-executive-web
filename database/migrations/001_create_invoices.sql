CREATE TABLE IF NOT EXISTS invoices (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    invoice_number VARCHAR(32) NOT NULL,
    journey_type ENUM('one_way', 'return') NOT NULL,
    route VARCHAR(180) NOT NULL,
    outbound_at DATETIME NOT NULL,
    return_at DATETIME NULL,
    aircraft_type VARCHAR(100) NOT NULL,
    capacity SMALLINT UNSIGNED NOT NULL,
    additional_request TEXT NULL,
    total_usd_cents BIGINT UNSIGNED NOT NULL,
    generated_at DATETIME NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY invoices_invoice_number_unique (invoice_number),
    KEY invoices_generated_at_index (generated_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
