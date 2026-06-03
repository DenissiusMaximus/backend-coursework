<div class="max-w-lg">
    <h1 class="text-xl font-semibold text-gray-800 mb-4">Нова транзакція</h1>

    <form method="POST" action="<?= url('transactions/store') ?>" class="bg-white border border-gray-200 rounded p-6 space-y-4">

        <div>
            <label class="block text-sm text-gray-600 mb-1">Сума (грн)</label>
            <input type="number" step="0.01" name="amount" required
                class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Тип</label>
            <select name="type" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                <option value="expense">Витрата</option>
                <option value="income">Дохід</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Категорія</label>
            <select name="category_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                <option value="">— Оберіть —</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat->id ?>"><?= htmlspecialchars($cat->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Джерело</label>
            <select name="source_id" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
                <option value="">— Оберіть —</option>
                <?php foreach ($sources as $src): ?>
                <option value="<?= $src->id ?>"><?= htmlspecialchars($src->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Дата</label>
            <input type="date" name="date" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Коментар</label>
            <input type="text" name="comment" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700">Зберегти</button>
            <a href="<?= url('transactions') ?>" class="text-sm text-gray-500 px-4 py-2 hover:underline">Скасувати</a>
        </div>
    </form>
</div>
