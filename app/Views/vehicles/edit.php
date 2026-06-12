<div class="card max-w-xl mx-auto">
    <div class="card-header">
        <i class="fas fa-edit text-green-600 text-xl"></i>
        Редактирование автомобиля
    </div>
    <form method="POST" action="/vehicles/<?= $vehicle['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <div class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Марка</label>
                    <input type="text" name="brand" value="<?= htmlspecialchars($vehicle['brand']) ?>" required class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Модель</label>
                    <input type="text" name="model" value="<?= htmlspecialchars($vehicle['model']) ?>" required class="form-input">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Государственный номер</label>
                <input type="text" name="license_plate" value="<?= htmlspecialchars($vehicle['license_plate']) ?>" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Текущий пробег (км)</label>
                <input type="number" name="current_mileage" value="<?= $vehicle['current_mileage'] ?>" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="available" <?= $vehicle['status'] === 'available' ? 'selected' : '' ?>>Доступен</option>
                    <option value="on_trip" <?= $vehicle['status'] === 'on_trip' ? 'selected' : '' ?>>В рейсе</option>
                    <option value="maintenance" <?= $vehicle['status'] === 'maintenance' ? 'selected' : '' ?>>Ремонт</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="/vehicles" class="btn-secondary inline-flex items-center gap-1">
                    <i class="fas fa-times"></i> Отмена
                </a>
                <button type="submit" class="btn-primary inline-flex items-center gap-1">
                    <i class="fas fa-save"></i> Обновить
                </button>
            </div>
        </div>
    </form>
</div>