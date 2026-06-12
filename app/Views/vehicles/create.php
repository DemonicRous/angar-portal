<div class="card max-w-xl mx-auto">
    <div class="card-header">
        <i class="fas fa-plus-circle text-green-600 text-xl"></i>
        Добавление автомобиля
    </div>
    <form method="POST" action="/vehicles">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <div class="space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Марка</label>
                    <input type="text" name="brand" required class="form-input" placeholder="Volvo" autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Модель</label>
                    <input type="text" name="model" required class="form-input" placeholder="FH16">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Государственный номер</label>
                <input type="text" name="license_plate" required class="form-input" placeholder="А123ВС 77">
            </div>
            <div class="form-group">
                <label class="form-label">Текущий пробег (км)</label>
                <input type="number" name="current_mileage" value="0" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Статус</label>
                <select name="status" class="form-select">
                    <option value="available">Доступен</option>
                    <option value="maintenance">Ремонт</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="/vehicles" class="btn-secondary inline-flex items-center gap-1">
                    <i class="fas fa-times"></i> Отмена
                </a>
                <button type="submit" class="btn-primary inline-flex items-center gap-1">
                    <i class="fas fa-save"></i> Сохранить
                </button>
            </div>
        </div>
    </form>
</div>