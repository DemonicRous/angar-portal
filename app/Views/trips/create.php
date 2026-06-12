<div class="card max-w-3xl mx-auto">
    <div class="card-header">
        <i class="fas fa-plus-circle text-blue-600 text-xl"></i>
        Создание нового рейса
    </div>
    <form method="POST" action="/trips">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-calendar-alt text-gray-400 mr-1"></i> Дата рейса
                </label>
                <input type="date" name="date" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-user text-gray-400 mr-1"></i> Водитель
                </label>
                <select name="driver_id" required class="form-select">
                    <option value="">Выберите водителя</option>
                    <?php foreach ($drivers as $driver): ?>
                        <option value="<?= $driver['id'] ?>">
                            <?= htmlspecialchars($driver['name']) ?> (тариф: <?= $driver['tariff_per_km'] ?> ₽/км)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-truck text-gray-400 mr-1"></i> Автомобиль
                </label>
                <select name="vehicle_id" required class="form-select">
                    <option value="">Выберите автомобиль</option>
                    <?php foreach ($vehicles as $vehicle): ?>
                        <option value="<?= $vehicle['id'] ?>">
                            <?= htmlspecialchars($vehicle['brand'] . ' ' . $vehicle['model'] . ' (' . $vehicle['license_plate'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-building text-gray-400 mr-1"></i> Организация-заказчик
                </label>
                <input type="text" name="client_organization" class="form-input" placeholder="ООО Ромашка">
            </div>
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> Город отправления
                </label>
                <input type="text" name="start_location" required class="form-input" placeholder="Москва">
            </div>
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-map-pin text-gray-400 mr-1"></i> Город назначения
                </label>
                <input type="text" name="end_location" required class="form-input" placeholder="Санкт-Петербург">
            </div>
            <div class="form-group md:col-span-2">
                <label class="form-label">
                    <i class="fas fa-tachometer-alt text-gray-400 mr-1"></i> Пробег на начало рейса (км)
                </label>
                <input type="number" name="start_mileage" required class="form-input" placeholder="например 12500">
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
            <a href="/trips" class="btn-secondary">
                <i class="fas fa-times"></i> Отмена
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Сохранить рейс
            </button>
        </div>
    </form>
</div>