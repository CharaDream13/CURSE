<?php

class Category
{
    public static function all(): array
    {
        return BDHelper::all(
            'SELECT c.id, c.code, c.name, c.description, c.capacity, p.price,
                    (SELECT COUNT(*) FROM rooms r WHERE r.category_id = c.id) AS rooms_count
             FROM room_categories c
             LEFT JOIN category_prices p ON p.category_id = c.id
                AND p.valid_from <= CURDATE()
                AND (p.valid_to IS NULL OR p.valid_to > CURDATE())
             ORDER BY c.id'
        );
    }

    public static function find(int $id): ?array
    {
        foreach (self::all() as $category) {
            if ((int) $category['id'] === $id) {
                return $category;
            }
        }
        return null;
    }

    public static function priceHistory(): array
    {
        return BDHelper::all(
            'SELECT p.id, c.name, p.price, p.valid_from, p.valid_to
             FROM category_prices p
             JOIN room_categories c ON c.id = p.category_id
             ORDER BY p.valid_from DESC, p.id DESC'
        );
    }

    public static function currentTariff(int $categoryId): ?array
    {
        return BDHelper::one(
            'SELECT id, valid_from FROM category_prices
             WHERE category_id = ? AND valid_from <= CURDATE() AND (valid_to IS NULL OR valid_to > CURDATE())
             FOR UPDATE',
            [$categoryId]
        );
    }

    public static function today(): string
    {
        return (string) BDHelper::value('SELECT CURDATE()');
    }

    public static function updateTariffPrice(int $tariffId, float $price): void
    {
        BDHelper::run('UPDATE category_prices SET price = ? WHERE id = ?', [$price, $tariffId]);
    }

    public static function closeTariff(int $tariffId, string $date): void
    {
        BDHelper::run('UPDATE category_prices SET valid_to = ? WHERE id = ?', [$date, $tariffId]);
    }

    public static function addTariff(int $categoryId, float $price, string $from): void
    {
        BDHelper::run(
            'INSERT INTO category_prices (category_id, price, valid_from, valid_to) VALUES (?, ?, ?, NULL)',
            [$categoryId, $price, $from]
        );
    }
}
