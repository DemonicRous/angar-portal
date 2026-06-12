/**
 * Глобальные вспомогательные функции для портала
 */

// Функция для отображения модального окна завершения рейса
function showCompleteModal(tripId, startMileage, driverTariff) {
    const modal = document.getElementById('completeModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Сохраняем данные в data-атрибутах модалки
    modal.dataset.tripId = tripId;
    modal.dataset.startMileage = startMileage;
    modal.dataset.driverTariff = driverTariff;

    // Устанавливаем action формы
    const form = document.getElementById('completeForm');
    if (form) {
        form.action = `/trips/${tripId}/complete`;
    }

    // Сброс полей
    document.getElementById('endMileage').value = '';
    document.getElementById('salaryPreview').innerText = '0';
}

// Функция закрытия модального окна
function closeModal() {
    const modal = document.getElementById('completeModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

// Расчёт зарплаты на лету при вводе конечного пробега
document.addEventListener('DOMContentLoaded', function() {
    const endMileageInput = document.getElementById('endMileage');
    if (endMileageInput) {
        endMileageInput.addEventListener('input', function(e) {
            const modal = document.getElementById('completeModal');
            if (!modal) return;

            const startMileage = parseInt(modal.dataset.startMileage);
            const tariff = parseFloat(modal.dataset.driverTariff);
            const endVal = parseInt(e.target.value);

            if (!isNaN(startMileage) && !isNaN(tariff) && !isNaN(endVal) && endVal > startMileage) {
                const distance = endVal - startMileage;
                const salary = distance * tariff;
                document.getElementById('salaryPreview').innerText = salary.toFixed(2);
            } else {
                document.getElementById('salaryPreview').innerText = '0';
            }
        });
    }

    // Подтверждение удаления
    const deleteButtons = document.querySelectorAll('.delete-confirm');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Вы уверены, что хотите удалить эту запись?')) {
                e.preventDefault();
            }
        });
    });
});

// Функция для динамической подгрузки фильтров (если нужно)
function applyFilters() {
    const form = document.getElementById('filterForm');
    if (form) form.submit();
}

// CSRF-токен для AJAX (если потребуется) – можно добавить позже
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
}