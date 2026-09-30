ALTER TABLE invoices
    ADD COLUMN recipient_email VARCHAR(254) NOT NULL DEFAULT '' AFTER invoice_recipient,
    ADD COLUMN recipient_country_code VARCHAR(8) NULL AFTER recipient_email,
    ADD COLUMN recipient_phone VARCHAR(20) NULL AFTER recipient_country_code;
