<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full space-y-6 bg-white rounded-2xl shadow-xl p-8">
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-truck-moving text-blue-600 text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900">Автопортал «Ангар»</h2>
            <p class="text-gray-500 mt-2">Войдите в свою учётную запись</p>
        </div>
        <form method="POST" action="/login" class="space-y-5">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <div>
                <label class="form-label">Электронная почта</label>
                <div class="relative">
                    <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="email" name="email" required class="form-input pl-10" placeholder="admin@angar.ru" autofocus>
                </div>
            </div>
            <div>
                <label class="form-label">Пароль</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="password" required class="form-input pl-10" placeholder="••••••">
                </div>
            </div>
            <button type="submit" class="btn-primary w-full py-3 text-base">
                <i class="fas fa-sign-in-alt"></i> Войти
            </button>
        </form>
        <div class="text-center text-xs text-gray-400 pt-4 border-t">
            Демо-доступ: admin@angar.ru / admin123
        </div>
    </div>
</div>