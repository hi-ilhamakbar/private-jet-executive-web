ALTER TABLE invoices
    ADD COLUMN invoice_recipient VARCHAR(180) NOT NULL DEFAULT '' AFTER invoice_number;
