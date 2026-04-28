<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Фінансовий планувальник' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <header class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 py-3 flex gap-6 items-center">
            <span class="font-semibold text-gray-800">💰 Фін. Планувальник</span>
            <nav class="flex gap-4 text-sm text-gray-600">
                <a href="<?= url() ?>" class="hover:text-gray-900">Головна</a>
                <a href="<?= url('transactions') ?>" class="hover:text-gray-900">Транзакції</a>
                <a href="<?= url('categories') ?>" class="hover:text-gray-900">Категорії</a>
                <a href="<?= url('sources') ?>" class="hover:text-gray-900">Джерела</a>
                <a href="<?= url('aims') ?>" class="hover:text-gray-900">Цілі</a>
                <a href="<?= url('users') ?>" class="hover:text-gray-900">Користувачі</a>
                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a href="<?= url('auth/seeAdmins') ?>" class="hover:text-gray-900">Адміністратори</a>
                <?php endif; ?>
            </nav>
            <div class="ml-auto flex gap-3 text-sm items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="text-gray-500 mr-2"><?= htmlspecialchars($_SESSION['email'] ?? '') ?></span>
                    <a href="<?= url('auth/logout') ?>" class="text-red-600 hover:text-red-800 px-3 py-1.5 border border-red-200 rounded">Вийти</a>
                <?php else: ?>
                    <a href="<?= url('auth/login') ?>" class="text-blue-600 hover:text-blue-800 px-3 py-1.5">Увійти</a>
                    <a href="<?= url('auth/register') ?>" class="bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700">Реєстрація</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-6">
        <?= $content ?>
    </main>
</body>
</html>