<?php
require_once 'vendor/autoload.php';
use App\Core\Database;

$pdo = Database::getConnection();

$email = 'admin@angar.ru';
$newPassword = 'admin123';

$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
$result = $stmt->execute([$hash, $email]);

if ($result && $stmt->rowCount() > 0) {
    echo "Пароль для пользователя $email успешно обновлён.\n";
    echo "Новый пароль: $newPassword\n";
} else {
    echo "Пользователь с email $email не найден или пароль не изменён.\n";
}