USE hotel_db;

INSERT INTO users (employee_id, username, password_hash, role, is_active) VALUES
(1, 'admin', '$2b$12$j9dgTeXIFrdXdRjrS.pf8OU/OEfyckjIMJ9rlDOxChYflKsZaKeYS', 'administrator', 1),
(2, 'manager', '$2b$12$aKnKBqghC91HbVe3snp5g.WiXR0.JR9xZ9dEHcrqgrXiMRR9UY3K6', 'manager', 1),
(3, 'staff', '$2b$12$FHXNE40kPxzuroqkDJFJFeTE0IZ1FOOEe5wux7N5eeDFyvCF3gcIa', 'housekeeping', 1);
