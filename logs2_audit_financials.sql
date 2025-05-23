-- SQL for logs2_audit_financials database

CREATE DATABASE IF NOT EXISTS logs2_audit_financials;
USE logs2_audit_financials;

-- Table: journalentries
CREATE TABLE IF NOT EXISTS journalentries (
    EntryID INT AUTO_INCREMENT PRIMARY KEY,
    AccountID INT NOT NULL,
    EntryType ENUM('Debit', 'Credit') NOT NULL,
    Amount DECIMAL(18,2) NOT NULL,
    EntryDate DATETIME NOT NULL,
    Description VARCHAR(255) DEFAULT NULL
);

-- Table: financial_audit_gl
CREATE TABLE IF NOT EXISTS financial_audit_gl (
    AuditID INT AUTO_INCREMENT PRIMARY KEY,
    EntryID INT NOT NULL,
    Status ENUM('Not Audited', 'Pending', 'Reviewed', 'Flagged') NOT NULL DEFAULT 'Not Audited',
    ReviewedBy VARCHAR(100) DEFAULT NULL,
    AuditDate DATE DEFAULT NULL,
    Notes TEXT DEFAULT NULL,
    FOREIGN KEY (EntryID) REFERENCES journalentries(EntryID) ON DELETE CASCADE
);

-- Sample data for journalentries
INSERT INTO journalentries (AccountID, EntryType, Amount, EntryDate, Description) VALUES
(101, 'Debit', 1000.00, '2025-05-01 09:00:00', 'Initial capital'),
(102, 'Credit', 500.00, '2025-05-02 10:30:00', 'Office supplies'),
(103, 'Debit', 200.00, '2025-05-03 14:15:00', 'Travel expense');

-- Sample data for financial_audit_gl
INSERT INTO financial_audit_gl (EntryID, Status, ReviewedBy, AuditDate, Notes) VALUES
(1, 'Reviewed', 'Auditor A', '2025-05-10', 'All documents complete.'),
(2, 'Flagged', 'Auditor B', '2025-05-11', 'Missing receipt.'),
(3, 'Pending', NULL, NULL, NULL);
