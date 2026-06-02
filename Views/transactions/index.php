<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold text-gray-800">Транзакції</h1>
    <a href="<?= url('transactions/create') ?>" class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700">+ Додати</a>
</div>

<div class="bg-white rounded border border-gray-200 overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Сума</th>
                <th class="px-4 py-3">Категорія</th>
                <th class="px-4 py-3">Джерело</th>
                <th class="px-4 py-3">Дата</th>
                <th class="px-4 py-3">Коментар</th>
                <th class="px-4 py-3">Дії</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php
            $categoryMap = [];
            foreach ($categories as $cat) {
                $categoryMap[$cat->id] = ['name' => $cat->name, 'type' => $cat->type];
            }
            $sourceMap = [];
            foreach ($sources as $src) {
                $sourceMap[$src->id] = $src->name;
            }
            ?>
            <?php foreach ($transactions as $t): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-500"><?= $t->id ?></td>
                <td class="px-4 py-3 font-medium <?= $t->amount >= 0 ? 'text-green-600' : 'text-red-500' ?>">
                    <?= number_format($t->amount, 2) ?> грн
                </td>
                <td class="px-4 py-3">
                    <?php if (isset($categoryMap[$t->category_id])): ?>
                        <span class="px-2 py-0.5 rounded text-xs <?= $categoryMap[$t->category_id]['type'] === 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' ?>">
                            <?= htmlspecialchars($categoryMap[$t->category_id]['name']) ?> — <?= $categoryMap[$t->category_id]['type'] === 'income' ? 'Дохід' : 'Витрата' ?>
                        </span>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td class="px-4 py-3"><?= htmlspecialchars($sourceMap[$t->source_id] ?? '—') ?></td>
                <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($t->date ?? '—') ?></td>
                <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($t->comment ?? '—') ?></td>
                <td class="px-4 py-3 flex gap-2">
                    <a href="<?= url('transactions/edit/' . $t->id) ?>" class="text-blue-600 hover:underline text-xs">Редагувати</a>
                    <form method="POST" action="<?= url('transactions/delete/' . $t->id) ?>" onsubmit="return confirm('Видалити?')">
                        <button class="text-red-500 hover:underline text-xs">Видалити</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($transactions)): ?>
            <tr><td colspan="7" class="px-4 py-6 text-center text-gray-400">Транзакцій немає</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>