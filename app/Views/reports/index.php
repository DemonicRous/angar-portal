<div class="card">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold flex items-center gap-2">
            <i class="fas fa-chart-line text-purple-500"></i> 
            Аналитика и отчёты
        </h1>
    </div>

    <!-- Фильтр по дате -->
    <form id="filterForm" class="bg-gray-50/90 rounded-xl p-5 mb-6 border border-gray-200">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div>
                <label class="form-label text-sm">Дата от</label>
                <input type="date" name="date_from" id="date_from" class="form-input text-sm" value="<?= date('Y-m-01') ?>">
            </div>
            <div>
                <label class="form-label text-sm">Дата до</label>
                <input type="date" name="date_to" id="date_to" class="form-input text-sm" value="<?= date('Y-m-t') ?>">
            </div>
            <button type="button" onclick="loadStats()" class="col-span-full lg:col-span-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-all shadow-sm">
                <i class="fas fa-chart-simple"></i> 
                Обновить отчёт
            </button>
        </div>
    </form>

    <!-- Карточки с итогами -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6" id="summary">
        <div class="bg-white rounded-xl shadow p-4 text-center animate-pulse">Загрузка...</div>
    </div>

    <!-- Графики -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-lg font-semibold mb-3 flex items-center gap-2"><i class="fas fa-road text-blue-500"></i> Пробег по водителям (км)</h3>
            <canvas id="tripsChart" height="250"></canvas>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <h3 class="text-lg font-semibold mb-3 flex items-center gap-2"><i class="fas fa-coins text-red-500"></i> Затраты по автомобилям</h3>
            <canvas id="expensesChart" height="250"></canvas>
        </div>
    </div>

    <!-- Таблицы статистики -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="font-semibold flex items-center gap-2"><i class="fas fa-users text-purple-600"></i> Статистика по водителям</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="driverTable">
                    <thead class="bg-gray-50">
                        <tr><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Водитель</th><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Пробег, км</th><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Зарплата, ₽</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="bg-gray-50 px-4 py-3 border-b">
                <h3 class="font-semibold flex items-center gap-2"><i class="fas fa-truck text-green-600"></i> Статистика по автомобилям</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="vehicleTable">
                    <thead class="bg-gray-50">
                        <tr><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Автомобиль</th><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Пробег, км</th><th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Затраты, ₽</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let tripsChart = null;
let expensesChart = null;

async function loadStats() {
    const dateFrom = document.getElementById('date_from').value;
    const dateTo = document.getElementById('date_to').value;
    
    // Показываем индикатор загрузки
    document.getElementById('summary').innerHTML = '<div class="col-span-full text-center py-4">Загрузка данных...</div>';
    
    try {
        const response = await fetch(`/api/stats?date_from=${dateFrom}&date_to=${dateTo}`);
        const data = await response.json();
        
        if (!data || !data.summary) {
            throw new Error('Нет данных');
        }
        
        // Обновляем карточки
        document.getElementById('summary').innerHTML = `
            <div class="bg-blue-50 rounded-xl p-4 text-center border border-blue-100">
                <div class="text-2xl font-bold text-blue-600">${data.summary.total_trips}</div>
                <div class="text-gray-600 text-sm">Рейсов</div>
            </div>
            <div class="bg-green-50 rounded-xl p-4 text-center border border-green-100">
                <div class="text-2xl font-bold text-green-600">${data.summary.total_distance.toFixed(0)} км</div>
                <div class="text-gray-600 text-sm">Общий пробег</div>
            </div>
            <div class="bg-orange-50 rounded-xl p-4 text-center border border-orange-100">
                <div class="text-2xl font-bold text-orange-600">${data.summary.total_salary.toFixed(2)} ₽</div>
                <div class="text-gray-600 text-sm">Зарплата</div>
            </div>
            <div class="bg-red-50 rounded-xl p-4 text-center border border-red-100">
                <div class="text-2xl font-bold text-red-600">${data.summary.total_expenses.toFixed(2)} ₽</div>
                <div class="text-gray-600 text-sm">Затраты</div>
            </div>
        `;
        
        // Таблица водителей
        const driverRows = data.driver_stats.map(d => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm">${escapeHtml(d.name)}</td>
                <td class="px-4 py-2 text-sm">${d.distance.toFixed(0)} км</td>
                <td class="px-4 py-2 text-sm font-semibold text-green-700">${d.salary.toFixed(2)} ₽</td>
            </tr>
        `).join('');
        document.querySelector('#driverTable tbody').innerHTML = driverRows || '<tr><td colspan="3" class="text-center py-4 text-gray-400">Нет данных</td></tr>';
        
        // Таблица автомобилей
        const vehicleRows = data.vehicle_stats.map(v => `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-2 text-sm">${escapeHtml(v.name)}</td>
                <td class="px-4 py-2 text-sm">${v.distance.toFixed(0)} км</td>
                <td class="px-4 py-2 text-sm font-semibold text-red-600">${v.expenses.toFixed(2)} ₽</td>
            </tr>
        `).join('');
        document.querySelector('#vehicleTable tbody').innerHTML = vehicleRows || '<tr><td colspan="3" class="text-center py-4 text-gray-400">Нет данных</td></tr>';
        
        // Графики
        if (tripsChart) tripsChart.destroy();
        if (expensesChart) expensesChart.destroy();
        
        const driverNames = data.driver_stats.map(d => d.name);
        const driverDistances = data.driver_stats.map(d => d.distance);
        
        const vehicleNames = data.vehicle_stats.map(v => v.name);
        const vehicleExpenses = data.vehicle_stats.map(v => v.expenses);
        
        tripsChart = new Chart(document.getElementById('tripsChart'), {
            type: 'bar',
            data: {
                labels: driverNames,
                datasets: [{
                    label: 'Пробег (км)',
                    data: driverDistances,
                    backgroundColor: '#3b82f6',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'top' } }
            }
        });
        
        expensesChart = new Chart(document.getElementById('expensesChart'), {
            type: 'pie',
            data: {
                labels: vehicleNames,
                datasets: [{
                    data: vehicleExpenses,
                    backgroundColor: ['#ef4444', '#f97316', '#eab308', '#10b981', '#06b6d4', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'right' } }
            }
        });
        
    } catch (error) {
        console.error(error);
        document.getElementById('summary').innerHTML = '<div class="col-span-full text-center py-4 text-red-500">Ошибка загрузки данных</div>';
    }
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Загружаем при загрузке страницы
document.addEventListener('DOMContentLoaded', loadStats);
</script>