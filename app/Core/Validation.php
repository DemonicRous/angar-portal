<?php

namespace App\Core;

/**
 * Класс для валидации входных данных.
 */
class Validation
{
    protected array $errors = [];

    /**
     * Проверяет, что поле обязательно и не пусто.
     *
     * @param string $field
     * @param mixed $value
     * @param string $message
     * @return $this
     */
    public function required(string $field, mixed $value, string $message = ''): self
    {
        if (empty($value) && $value !== '0') {
            $this->errors[$field][] = $message ?: "Поле {$field} обязательно для заполнения.";
        }
        return $this;
    }

    /**
     * Проверяет, что поле имеет числовой тип и неотрицательное (опционально).
     *
     * @param string $field
     * @param mixed $value
     * @param bool $nonNegative
     * @param string $message
     * @return $this
     */
    public function numeric(string $field, mixed $value, bool $nonNegative = true, string $message = ''): self
    {
        if (!is_numeric($value)) {
            $this->errors[$field][] = $message ?: "Поле {$field} должно быть числом.";
        } elseif ($nonNegative && $value < 0) {
            $this->errors[$field][] = $message ?: "Поле {$field} не может быть отрицательным.";
        }
        return $this;
    }

    /**
     * Проверяет минимальную длину строки.
     *
     * @param string $field
     * @param string $value
     * @param int $min
     * @param string $message
     * @return $this
     */
    public function minLength(string $field, string $value, int $min, string $message = ''): self
    {
        if (strlen($value) < $min) {
            $this->errors[$field][] = $message ?: "Поле {$field} должно содержать не менее {$min} символов.";
        }
        return $this;
    }

    /**
     * Проверяет, что значение существует в заданном массиве (например, статус).
     *
     * @param string $field
     * @param mixed $value
     * @param array $allowed
     * @param string $message
     * @return $this
     */
    public function in(string $field, mixed $value, array $allowed, string $message = ''): self
    {
        if (!in_array($value, $allowed)) {
            $this->errors[$field][] = $message ?: "Недопустимое значение поля {$field}.";
        }
        return $this;
    }

    /**
     * Возвращает true, если ошибок нет.
     *
     * @return bool
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Возвращает массив ошибок.
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }
}