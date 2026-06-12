<div class="card max-w-2xl mx-auto">
    <div class="card-header">
        <i class="fas fa-user-edit text-purple-600 text-xl"></i>
        Редактирование водителя
    </div>
    <form method="POST" action="/drivers/<?= $driver['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <div class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Имя и фамилия</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required class="form-input">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Новый пароль (оставьте пустым, чтобы не менять)</label>
                    <input type="password" name="password" class="form-input" placeholder="Новый пароль">
                </div>
                <div class="form-group">
                    <label class="form-label">Телефон</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($driver['phone'] ?? '') ?>" class="form-input">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Тариф за километр (₽)</label>
                <input type="number" step="0.01" name="tariff_per_km" value="<?= $driver['tariff_per_km'] ?>" required class="form-input">
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="/drivers" class="btn-secondary inline-flex items-center gap-1">
                    <i class="fas fa-times"></i> Отмена
                </a>
                <button type="submit" class="btn-primary inline-flex items-center gap-1">
                    <i class="fas fa-save"></i> Обновить
                </button>
            </div>
        </div>
    </form>
</div>