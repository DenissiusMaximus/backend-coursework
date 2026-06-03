<?php
$uniqueCategories = [];
if (isset($categories) && is_array($categories)) {
    foreach ($categories as $cat) {
        if (!isset($uniqueCategories[$cat->name])) {
            $uniqueCategories[$cat->name] = $cat;
        }
    }
}
?>
<div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 gap-3">
    <h1 class="text-xl font-semibold text-gray-800">Транзакції</h1>
    <div class="flex flex-col sm:flex-row gap-3 items-center">
        <select id="filter-type" class="border border-gray-300 rounded px-3 py-1.5 text-sm" onchange="applyFilters()">
            <option value="">Усі типи</option>
            <option value="income">Доходи</option>
            <option value="expense">Витрати</option>
        </select>
        <select id="filter-category" class="border border-gray-300 rounded px-3 py-1.5 text-sm" onchange="applyFilters()">
            <option value="">Усі категорії</option>
            <?php foreach ($uniqueCategories as $name => $cat): ?>
                <option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="date" id="filter-date" class="border border-gray-300 rounded px-3 py-1.5 text-sm" onchange="applyFilters()">
        <a href="<?= url('transactions/create') ?>" class="bg-blue-600 text-white text-sm px-4 py-2 rounded hover:bg-blue-700 shrink-0">+ Додати</a>
    </div>
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

<script>
    let limit = 20;
    let offset = 0;
    const baseUrl = '<?= rtrim(url(''), '/') ?>';

    function applyFilters() {
        offset = 0;
        document.getElementById('transactions-list').innerHTML = '';
        loadTransactions();
    }

    async function loadTransactions() {
        const btn = document.getElementById('load-more-btn');
        btn.disabled = true;
        btn.textContent = '...';

        const categoryName = document.getElementById('filter-category').value;
        const type = document.getElementById('filter-type').value;
        const date = document.getElementById('filter-date').value;
        
        let fetchUrl = `${baseUrl}/transactions/api/filtered?limit=${limit}&offset=${offset}`;
        if (categoryName) fetchUrl += `&category_name=${encodeURIComponent(categoryName)}`;
        if (type) fetchUrl += `&type=${type}`;
        if (date) fetchUrl += `&date=${date}`;

        try {
            const res = await fetch(fetchUrl);
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