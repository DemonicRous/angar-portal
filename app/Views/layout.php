<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <title>Автопортал «Ангар»</title>
    <!-- Tailwind + Google Fonts + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f4f7fc; }
        .card { @apply bg-white rounded-2xl shadow-md p-6 transition-all hover:shadow-lg; }
        .btn-primary { @apply bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-5 rounded-xl transition duration-200 shadow-sm; }
        .btn-secondary { @apply bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-5 rounded-xl transition; }
        .btn-danger { @apply bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-xl transition; }
        .form-input { @apply border border-gray-300 rounded-xl p-3 w-full focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none transition; }
        .form-label { @apply block text-gray-700 font-medium mb-1 text-sm; }
        .badge { @apply px-2 py-1 rounded-full text-xs font-semibold; }
        .badge-green { @apply bg-green-100 text-green-800; }
        .badge-yellow { @apply bg-yellow-100 text-yellow-800; }
        .badge-red { @apply bg-red-100 text-red-800; }
        .badge-blue { @apply bg-blue-100 text-blue-800; }
        .table th { @apply px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider bg-gray-50 border-b; }
        .table td { @apply px-4 py-3 border-b border-gray-200 text-sm; }
        .table tr:hover { @apply bg-gray-50 transition; }

        .form-group {
    margin-bottom: 1rem;
}
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    font-size: 0.875rem;
    color: #1e293b;
}
.form-input, .form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    transition: all 0.2s;
}
.form-input:focus, .form-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
}
.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background-color: #2563eb;
    color: white;
    padding: 0.625rem 1.25rem;
    font-weight: 500;
    border-radius: 0.75rem;
    transition: all 0.2s;
}
.btn-primary:hover {
    background-color: #1d4ed8;
    transform: translateY(-1px);
}
.btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background-color: #64748b;
    color: white;
    padding: 0.625rem 1.25rem;
    font-weight: 500;
    border-radius: 0.75rem;
    transition: all 0.2s;
}
.btn-secondary:hover {
    background-color: #475569;
}
.card-header {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #eef2ff;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.form-group { margin-bottom: 1.5rem; }
.form-input, .form-select { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.75rem; ... }
.form-select { background-color: #fff; }
.btn-primary { background-color: #2563eb; color: white; ... }
.btn-secondary { background-color: #64748b; color: white; ... }
.card-header { font-size: 1.25rem; font-weight: 600; margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 1px solid #eef2ff; display: flex; align-items: center; gap: 0.5rem; }
.badge { @apply px-2 py-1 rounded-full text-xs font-semibold; }
.badge-green { @apply bg-green-100 text-green-800; }
.badge-yellow { @apply bg-yellow-100 text-yellow-800; }
.badge-red { @apply bg-red-100 text-red-800; }

.form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    transition: all 0.2s ease;
}
.form-textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
}
.badge-blue {
    background-color: #dbeafe;
    color: #1e40af;
}
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-sans antialiased">
    <div x-data="{ mobileMenuOpen: false }" class="min-h-screen flex flex-col">
        <!-- Навигация -->
        <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center space-x-8">
                        <a href="/" class="flex items-center space-x-2">
                            <i class="fas fa-truck-moving text-blue-600 text-2xl"></i>
                            <span class="font-bold text-xl text-gray-800">Ангар</span>
                        </a>
                        <?php if (\App\Core\Auth::isLoggedIn()): ?>
                        <div class="hidden md:flex space-x-1">
                            <a href="/trips" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition flex items-center gap-2"><i class="fas fa-route"></i><span>Рейсы</span></a>
                            <a href="/vehicles" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition flex items-center gap-2"><i class="fas fa-truck"></i><span>Автопарк</span></a>
                            <?php if (\App\Core\Auth::isAdmin() || \App\Core\Auth::isDispatcher()): ?>
                            <a href="/drivers" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition flex items-center gap-2"><i class="fas fa-users"></i><span>Водители</span></a>
                            <a href="/expenses" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition flex items-center gap-2"><i class="fas fa-coins"></i><span>Затраты</span></a>
                            <a href="/reports" class="px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100 hover:text-blue-600 transition flex items-center gap-2"><i class="fas fa-chart-line"></i><span>Аналитика</span></a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center space-x-4">
                        <?php if (\App\Core\Auth::isLoggedIn()): ?>
                            <div class="hidden md:flex items-center gap-2 bg-gray-100 px-3 py-1 rounded-full">
                                <i class="fas fa-user-circle text-gray-500"></i>
                                <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                                <span class="text-xs text-gray-400">(<?= $_SESSION['role_name'] ?>)</span>
                            </div>
                            <a href="/logout" class="text-red-500 hover:text-red-700 transition flex items-center gap-1"><i class="fas fa-sign-out-alt"></i><span class="hidden md:inline">Выход</span></a>
                        <?php else: ?>
                            <a href="/login" class="btn-primary py-2 px-4"><i class="fas fa-sign-in-alt mr-1"></i>Вход</a>
                        <?php endif; ?>
                        <!-- Мобильное меню -->
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Мобильное меню -->
            <div x-show="mobileMenuOpen" x-cloak class="md:hidden border-t border-gray-200 bg-white">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <a href="/trips" class="flex items-center gap-2 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100"><i class="fas fa-route"></i>Рейсы</a>
                    <a href="/vehicles" class="flex items-center gap-2 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100"><i class="fas fa-truck"></i>Автопарк</a>
                    <?php if (\App\Core\Auth::isAdmin() || \App\Core\Auth::isDispatcher()): ?>
                    <a href="/drivers" class="flex items-center gap-2 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100"><i class="fas fa-users"></i>Водители</a>
                    <a href="/expenses" class="flex items-center gap-2 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100"><i class="fas fa-coins"></i>Затраты</a>
                    <a href="/reports" class="flex items-center gap-2 px-3 py-2 rounded-md text-gray-700 hover:bg-gray-100"><i class="fas fa-chart-line"></i>Аналитика</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
            <!-- Flash сообщения с анимацией -->
            <?php if (isset($_SESSION['flash']['success'])): ?>
                <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-sm flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    <span><?= htmlspecialchars($_SESSION['flash']['success']) ?></span>
                    <button @click="show = false" class="ml-auto text-green-500"><i class="fas fa-times"></i></button>
                </div>
                <?php unset($_SESSION['flash']['success']); ?>
            <?php endif; ?>
            <?php if (isset($_SESSION['flash']['error'])): ?>
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                    <span><?= htmlspecialchars($_SESSION['flash']['error']) ?></span>
                    <button @click="show = false" class="ml-auto text-red-500"><i class="fas fa-times"></i></button>
                </div>
                <?php unset($_SESSION['flash']['error']); ?>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </main>

        <footer class="bg-white border-t border-gray-200 py-4 text-center text-gray-400 text-sm">
            © <?= date('Y') ?> Автопортал «Ангар». Все права защищены.
        </footer>
    </div>
    <script src="/assets/js/main.js"></script>
</body>
</html>