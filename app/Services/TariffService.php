<?php

class TariffService
{
    public static function change(int $categoryId, string $rawPrice, int $userId): void
    {
        $normalized = str_replace(',', '.', str_replace(' ', '', $rawPrice));
        if (!is_numeric($normalized)) {
            throw new InvalidArgumentException('Укажите цену числом');
        }
        $price = round((float) $normalized, 2);
        if ($price < 1 || $price > 1000000) {
            throw new InvalidArgumentException('Цена должна быть от 1 до 1 000 000');
        }
        if (!Category::find($categoryId)) {
            throw new InvalidArgumentException('Категория не найдена');
        }

        $pdo = BDHelper::getConnection();
        $pdo->beginTransaction();
        try {
            $today = Category::today();
            $current = Category::currentTariff($categoryId);
            if ($current && $current['valid_from'] === $today) {
                Category::updateTariffPrice((int) $current['id'], $price);
            } else {
                if ($current) {
                    Category::closeTariff((int) $current['id'], $today);
                }
                Category::addTariff($categoryId, $price, $today);
            }
            Audit::log($userId, 'tariff_change', 'category', $categoryId, (string) $price);
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
