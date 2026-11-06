<h1>Журнал действий</h1>
<div class="table-wrap">
    <table>
        <tr><th>Время</th><th>Пользователь</th><th>Действие</th><th>Объект</th><th>Подробности</th></tr>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= e($log['created_at']) ?></td>
                <td><?= e($log['username'] ?? '—') ?></td>
                <td><?= e($log['action']) ?></td>
                <td><?= e($log['entity_type'] ? $log['entity_type'] . ' #' . $log['entity_id'] : '—') ?></td>
                <td><?= e($log['details']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
