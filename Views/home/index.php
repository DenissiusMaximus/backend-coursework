<h1 class="text-xl font-semibold text-gray-800 mb-6">Огляд</h1>

<!-- Cards row -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-blue-600 text-white rounded p-5 shadow-sm">
        <div class="text-sm opacity-80 mb-1">Загальний баланс</div>
        <div class="text-2xl font-bold"><?= number_format($totalBalance, 2) ?> ₴</div>
    </div>
    <a href="<?= url('transactions') ?>" class="block bg-white border border-gray-200 rounded p-5 hover:border-blue-400 hover:shadow-sm transition">
        <div class="text-2xl mb-2">💸</div>
        <div class="font-medium text-gray-800 text-sm">Транзакції</div>
    </a>
    <a href="<?= url('sources') ?>" class="block bg-white border border-gray-200 rounded p-5 hover:border-blue-400 hover:shadow-sm transition">
        <div class="text-2xl mb-2">🏦</div>
        <div class="font-medium text-gray-800 text-sm">Джерела</div>
    </a>
    <a href="<?= url('categories') ?>" class="block bg-white border border-gray-200 rounded p-5 hover:border-blue-400 hover:shadow-sm transition">
        <div class="text-2xl mb-2">🏷️</div>
        <div class="font-medium text-gray-800 text-sm">Категорії</div>
    </a>
</div>

<!-- Sources -->
<div class="mb-8">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Ваші рахунки</h2>
        <a href="<?= url('sources/create') ?>" class="text-sm text-blue-600 hover:underline">+ Додати рахунок</a>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <?php foreach ($sources as $source): ?>
        <div class="bg-white border border-gray-200 rounded p-4 flex flex-col justify-between">
            <div class="font-medium text-gray-800 truncate"><?= htmlspecialchars($source->name) ?></div>
            <div class="text-lg font-semibold mt-2 <?= $source->balance >= 0 ? 'text-green-600' : 'text-red-500' ?>">
                <?= number_format($source->balance, 2) ?> ₴
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($sources)): ?>
            <div class="text-gray-500 text-sm col-span-full">У вас ще немає жодного джерела (рахунку).</div>
        <?php endif; ?>
    </div>
</div>

<!-- Transactions -->
<div>
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold text-gray-800">Останні транзакції</h2>
        <a href="<?= url('transactions/create') ?>" class="text-sm text-blue-600 hover:underline">+ Нова транзакція</a>
    </div>

    <div class="bg-white rounded border border-gray-200 overflow-hidden mb-4">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs hidden md:table-header-group">
                <tr>
                    <th class="px-4 py-3">Сума</th>
                    <th class="px-4 py-3">Категорія</th>
                    <th class="px-4 py-3">Джерело</th>
                    <th class="px-4 py-3">Дата</th>
                    <th class="px-4 py-3">Коментар</th>
                    <th class="px-4 py-3 text-right">Дії</th>
                </tr>
            </thead>
            <tbody id="transactions-list" class="divide-y divide-gray-100">
            </tbody>
        </table>
    </div>
    <div class="text-center">
        <button id="load-more-btn" class="bg-gray-100 text-gray-700 hover:bg-gray-200 px-4 py-2 rounded text-sm transition">
            Завантажити ще
        </button>
    </div>
</div>

<script>
    let limit = 10;
    let offset = 0;
    const baseUrl = '<?= rtrim(url(''), '/') ?>';

    async function loadTransactions() {
        const btn = document.getElementById('load-more-btn');
        btn.disabled = true;
        btn.textContent = '...';

        try {
            const res = await fetch(`${baseUrl}/transactions/api?limit=${limit}&offset=${offset}`);
            const data = await res.json();

            if (data.length < limit) {
                btn.style.display = 'none';
            } else {
                btn.style.display = 'inline-block';
            }

            const tbody = document.getElementById('transactions-list');
            
            if (data.length === 0 && offset === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-4 py-6 text-center text-gray-400">Транзакцій немає</td></tr>';
                return;
            }

            data.forEach(t => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 flex flex-col md:table-row';
                
                const amountColor = t.is_income ? 'text-green-600' : 'text-red-500';
                const formattedAmount = parseFloat(t.amount).toFixed(2);
                
                tr.innerHTML = `
                    <td class="px-4 py-3 font-medium ${amountColor}">
                        <span class="md:hidden font-normal text-gray-500 text-xs uppercase mr-2">Сума:</span>
                        ${formattedAmount} ₴
                    </td>
                    <td class="px-4 py-2 md:py-3">
                        <span class="md:hidden font-normal text-gray-500 text-xs uppercase mr-2">Категорія:</span>
                        ${escapeHtml(t.category_name)}
                    </td>
                    <td class="px-4 py-2 md:py-3">
                        <span class="md:hidden font-normal text-gray-500 text-xs uppercase mr-2">Джерело:</span>
                        ${escapeHtml(t.source_name)}
                    </td>
                    <td class="px-4 py-2 md:py-3 text-gray-500">
                        <span class="md:hidden font-normal text-gray-500 text-xs uppercase mr-2">Дата:</span>
                        ${escapeHtml(t.date || '—')}
                    </td>
                    <td class="px-4 py-2 md:py-3 text-gray-500">
                        <span class="md:hidden font-normal text-gray-500 text-xs uppercase mr-2">Коментар:</span>
                        ${escapeHtml(t.comment || '—')}
                    </td>
                    <td class="px-4 py-3 md:text-right flex gap-3 md:justify-end border-t md:border-0 mt-2 md:mt-0">
                        <a href="${baseUrl}/transactions/edit/${t.id}" class="text-blue-600 hover:underline text-xs">Редагувати</a>
                        <form method="POST" action="${baseUrl}/transactions/delete/${t.id}" onsubmit="return confirm('Видалити?')">
                            <button class="text-red-500 hover:underline text-xs">Видалити</button>
                        </form>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            offset += limit;
        } catch (e) {
            console.error('Failed to load transactions', e);
            alert('Помилка завантаження транзакцій');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Завантажити ще';
        }
    }

    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return unsafe
             .toString()
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    document.getElementById('load-more-btn').addEventListener('click', loadTransactions);

    document.addEventListener('DOMContentLoaded', () => {
        loadTransactions();
    });
</script>
