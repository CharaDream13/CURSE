<?php

class Audit
{
    public static function log(?int $userId, string $action, ?string $entityType = null, ?int $entityId = null, ?string $details = null): void
    {
        BDHelper::run(
            'INSERT INTO audit_logs (user_id, action, entity_type, entity_id, details) VALUES (?, ?, ?, ?, ?)',
            [$userId, $action, $entityType, $entityId, $details]
        );
    }

    public static function recent(int $limit): array
    {
        return BDHelper::all(
            'SELECT a.id, a.action, a.entity_type, a.entity_id, a.details, a.created_at, u.username
             FROM audit_logs a
             LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.id DESC
             LIMIT ' . (int) $limit
        );
    }

    public static function failedLogins(string $username): int
    {
        return (int) BDHelper::value(
            "SELECT COUNT(*) FROM audit_logs
             WHERE action = 'login_failed' AND details = ? AND created_at > (NOW() - INTERVAL 10 MINUTE)",
            [$username]
        );
    }
}
