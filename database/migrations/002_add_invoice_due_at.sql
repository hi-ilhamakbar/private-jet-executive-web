ALTER TABLE invoices
    ADD COLUMN due_at DATETIME NOT NULL AFTER generated_at,
    ADD KEY invoices_due_at_index (due_at);
