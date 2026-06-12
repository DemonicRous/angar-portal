-- Демо-данные для портала "Ангар"
-- Добавляет диспетчера, водителей, автомобили, рейсы, затраты

-- =====================================================
-- 1. Диспетчер (пароль: password)
-- =====================================================
INSERT IGNORE INTO users (name, email, password, role_id)
SELECT 'Анна Смирнова', 'dispatcher@angar.ru', '$2y$10$92IXUNpkjO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO', id
FROM roles WHERE name = 'dispatcher' LIMIT 1;

-- =====================================================
-- 2. Водители (пароль для всех: password)
-- =====================================================

-- Водитель 1: Иван Петров
INSERT IGNORE INTO users (name, email, password, role_id) VALUES 
('Иван Петров', 'ivan.petrov@angar.ru', '$2y$10$92IXUNpkjO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO', (SELECT id FROM roles WHERE name = 'driver'));
SET @driver1_user = LAST_INSERT_ID();
INSERT IGNORE INTO drivers (user_id, phone, tariff_per_km) VALUES (@driver1_user, '+7 (901) 123-45-67', 12.50);

-- Водитель 2: Сергей Козлов
INSERT IGNORE INTO users (name, email, password, role_id) VALUES 
('Сергей Козлов', 'sergey.kozlov@angar.ru', '$2y$10$92IXUNpkjO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO', (SELECT id FROM roles WHERE name = 'driver'));
SET @driver2_user = LAST_INSERT_ID();
INSERT IGNORE INTO drivers (user_id, phone, tariff_per_km) VALUES (@driver2_user, '+7 (902) 234-56-78', 11.80);

-- Водитель 3: Михаил Соколов
INSERT IGNORE INTO users (name, email, password, role_id) VALUES 
('Михаил Соколов', 'mikhail.sokolov@angar.ru', '$2y$10$92IXUNpkjO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO0rO', (SELECT id FROM roles WHERE name = 'driver'));
SET @driver3_user = LAST_INSERT_ID();
INSERT IGNORE INTO drivers (user_id, phone, tariff_per_km) VALUES (@driver3_user, '+7 (903) 345-67-89', 13.00);

-- =====================================================
-- 3. Автомобили
-- =====================================================
INSERT IGNORE INTO vehicles (brand, model, license_plate, current_mileage, status) VALUES 
('Volvo', 'FH16', 'А123ВС 77', 245000, 'available'),
('Scania', 'R450', 'В456ЕК 77', 189500, 'available'),
('MAN', 'TGX', 'С789АВ 77', 312000, 'maintenance'),
('Mercedes-Benz', 'Actros', 'К012ММ 77', 98000, 'on_trip');

-- =====================================================
-- 4. Рейсы (часть завершённых, часть запланированных)
-- =====================================================
SET @d1 = (SELECT id FROM drivers WHERE user_id = (SELECT id FROM users WHERE email = 'ivan.petrov@angar.ru') LIMIT 1);
SET @d2 = (SELECT id FROM drivers WHERE user_id = (SELECT id FROM users WHERE email = 'sergey.kozlov@angar.ru') LIMIT 1);
SET @d3 = (SELECT id FROM drivers WHERE user_id = (SELECT id FROM users WHERE email = 'mikhail.sokolov@angar.ru') LIMIT 1);
SET @v1 = (SELECT id FROM vehicles WHERE license_plate = 'А123ВС 77' LIMIT 1);
SET @v2 = (SELECT id FROM vehicles WHERE license_plate = 'В456ЕК 77' LIMIT 1);
SET @v3 = (SELECT id FROM vehicles WHERE license_plate = 'С789АВ 77' LIMIT 1);
SET @v4 = (SELECT id FROM vehicles WHERE license_plate = 'К012ММ 77' LIMIT 1);

INSERT IGNORE INTO trips (driver_id, vehicle_id, date, start_location, end_location, client_organization, start_mileage, end_mileage, distance, calculated_salary, status) VALUES
-- Завершённые рейсы
(@d1, @v1, '2025-04-15', 'Москва', 'Санкт-Петербург', 'ООО "Логистик-Групп"', 245000, 245750, 750, 750 * 12.50, 'completed'),
(@d2, @v2, '2025-04-18', 'Казань', 'Нижний Новгород', 'ИП "Торговый дом"', 189500, 189820, 320, 320 * 11.80, 'completed'),
(@d3, @v4, '2025-04-20', 'Ростов-на-Дону', 'Краснодар', 'ООО "АгроТрейд"', 98000, 98280, 280, 280 * 13.00, 'completed'),
(@d1, @v1, '2025-05-05', 'Санкт-Петербург', 'Москва', 'ЗАО "ТрансСервис"', 245750, 246500, 750, 750 * 12.50, 'completed'),
(@d2, @v2, '2025-05-10', 'Нижний Новгород', 'Москва', 'ООО "Ритейл-Поставка"', 189820, 190480, 660, 660 * 11.80, 'completed'),
-- Запланированные рейсы
(@d1, @v1, '2025-06-15', 'Москва', 'Воронеж', 'ПАО "Черноземье"', 246500, NULL, NULL, NULL, 'planned'),
(@d2, @v2, '2025-06-20', 'Москва', 'Тверь', 'ООО "Верхневолжье"', 190480, NULL, NULL, NULL, 'planned'),
(@d3, @v4, '2025-06-25', 'Краснодар', 'Сочи', 'ИП "Курорт-Логистик"', 98280, NULL, NULL, NULL, 'planned');

-- =====================================================
-- 5. Затраты на ТС
-- =====================================================
INSERT IGNORE INTO expenses (vehicle_id, date, amount, category, comment) VALUES
(@v1, '2025-04-10', 7500, 'repair', 'Замена тормозных колодок'),
(@v1, '2025-04-25', 18500, 'maintenance', 'Плановое ТО-5'),
(@v2, '2025-04-16', 12000, 'fuel', 'Дизельное топливо 150 л'),
(@v2, '2025-05-05', 3200, 'repair', 'Замена масла'),
(@v4, '2025-04-22', 4500, 'repair', 'Ремонт стартера'),
(@v4, '2025-05-15', 8900, 'other', 'Шиномонтаж'),
(@v1, '2025-05-12', 6200, 'fuel', 'Топливо 75 л'),
(@v2, '2025-05-20', 15000, 'maintenance', 'Замена ремня ГРМ'),
(@v3, '2025-04-28', 28000, 'repair', 'Капитальный ремонт двигателя'),
(@v3, '2025-05-10', 5500, 'other', 'Замена аккумулятора');