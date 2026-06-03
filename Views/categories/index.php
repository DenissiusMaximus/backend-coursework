<div class="flex justify-between items-center mb-4">
    <h1 class="text-xl font-semibold text-gray-800">Категорії</h1>
    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
        <a href="<?= url('categories/create') ?>" class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700">+ Додати</a>
    <?php endif; ?>
</div>

<div class="bg-white rounded border border-gray-200 overflow-hidden">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">ID</th>
                <th class="px-4 py-3">Назва</th>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <th class="px-4 py-3">Дії</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($categories as $cat): ?>
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-500"><?= $cat->id ?></td>
                <td class="px-4 py-3"><?= htmlspecialchars($cat->name) ?></td>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="<?= url('categories/edit/' . $cat->id) ?>" class="text-blue-600 hover:underline text-xs">Редагувати</a>
                        <form method="POST" action="<?= url('categories/delete/' . $cat->id) ?>" onsubmit="return confirm('Видалити?')">
                            <button class="text-red-500 hover:underline text-xs">Видалити</button>
                        </form>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
            <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">Категорій немає</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
