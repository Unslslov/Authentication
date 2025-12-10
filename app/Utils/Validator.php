<?php

namespace App\Utils;

class Validator
{
    private array $errors = [];
    private array $data = [];
    private array $rules = [];

    /**
     * Установка данных для валидации
     */
    public function validate(array $data, array $rules): self
    {
        $this->errors = [];
        $this->data = $data;
        $this->rules = $rules;

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }

        return $this;
    }

    /**
     * Применение правил валидации
     */
    private function applyRule(string $field, $value, string $rule): void
    {
        $params = [];

        // Проверяем, есть ли параметры у правила (например: min:6)
        if (strpos($rule, ':') !== false) {
            [$rule, $param] = explode(':', $rule, 2);
            $params = explode(',', $param);
        }

        $methodName = 'validate' . ucfirst($rule);

        if (method_exists($this, $methodName)) {
            array_unshift($params, $field, $value);
            call_user_func_array([$this, $methodName], $params);
        }
    }

    /**
     * Проверка на ошибки
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Получение ошибок
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Получение первой ошибки для поля
     */
    public function first(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Получение валидированных данных
     */
    public function validated(): array
    {
        if ($this->fails()) {
            throw new \Exception("Cannot get validated data when validation fails.");
        }

        $validated = [];
        foreach (array_keys($this->rules) as $field) {
            $validated[$field] = $this->data[$field] ?? null;
        }

        return $validated;
    }

    // ============ МЕТОДЫ ВАЛИДАЦИИ ============

    /**
     * Обязательное поле
     */
    private function validateRequired(string $field, $value): void
    {
        if (is_null($value) || $value === '' || (is_array($value) && empty($value))) {
            $this->addError($field, "Поле {$field} обязательно для заполнения.");
        }
    }

    /**
     * Email
     */
    private function validateEmail(string $field, $value): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, "Введите корректный email адрес.");
        }
    }

    /**
     * Минимальная длина
     */
    private function validateMin(string $field, $value, int $min): void
    {
        if (!empty($value) && mb_strlen($value) < $min) {
            $this->addError($field, "Минимальная длина поля {$field} - {$min} символов.");
        }
    }

    /**
     * Максимальная длина
     */
    private function validateMax(string $field, $value, int $max): void
    {
        if (!empty($value) && mb_strlen($value) > $max) {
            $this->addError($field, "Максимальная длина поля {$field} - {$max} символов.");
        }
    }

    /**
     * Совпадение полей (например, подтверждение пароля)
     */
    private function validateConfirmed(string $field, $value, string $confirmationField = null): void
    {
        if ($confirmationField === null) {
            $confirmationField = $field . '_confirmation';
        }

        $confirmationValue = $this->data[$confirmationField] ?? null;

        if ($value !== $confirmationValue) {
            $this->addError($field, "Поле {$field} не совпадает с подтверждением.");
        }
    }


    /**
     * Телефон (базовая проверка)
     */
    private function validatePhone(string $field, $value): void
    {
        if (!empty($value)) {
            // Убираем все нецифровые символы, кроме плюса в начале
            $cleanPhone = preg_replace('/[^\d+]/', '', $value);

            // Проверяем минимальную длину (например, 10 цифр без кода страны)
            if (strlen($cleanPhone) < 10) {
                $this->addError($field, "Введите корректный номер телефона.");
            }
        }
    }

    /**
     * Число
     */
    private function validateNumeric(string $field, $value): void
    {
        if (!empty($value) && !is_numeric($value)) {
            $this->addError($field, "Поле {$field} должно быть числом.");
        }
    }

    /**
     * Целое число
     */
    private function validateInteger(string $field, $value): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
            $this->addError($field, "Поле {$field} должно быть целым числом.");
        }
    }
    /**
     * Валидация даты
     */
    private function validateDate(string $field, $value): void
    {
        if (!empty($value)) {
            $date = date_parse($value);
            if (!checkdate($date['month'], $date['day'], $date['year'])) {
                $this->addError($field, "Поле {$field} должно быть корректной датой.");
            }
        }
    }

    /**
     * Валидация URL
     */
    private function validateUrl(string $field, $value): void
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
            $this->addError($field, "Поле {$field} должно быть корректным URL.");
        }
    }

    /**
     * Валидация boolean
     */
    private function validateBoolean(string $field, $value): void
    {
        if (!empty($value) && !in_array($value, [true, false, 1, 0, '1', '0'], true)) {
            $this->addError($field, "Поле {$field} должно быть булевым значением.");
        }
    }

    /**
     * Пользовательская функция валидации
     */
    private function validateCustom(string $field, $value, callable $callback): void
    {
        if (!empty($value) && !$callback($value)) {
            $this->addError($field, "Поле {$field} не прошло валидацию.");
        }
    }

    /**
     * Проверка на существование в массиве
     */
    private function validateIn(string $field, $value, array $allowed): void
    {
        if (!empty($value) && !in_array($value, $allowed, true)) {
            $this->addError($field, "Поле {$field} содержит недопустимое значение.");
        }
    }

    /**
     * Проверка на отсутствие в массиве
     */
    private function validateNotIn(string $field, $value, array $disallowed): void
    {
        if (!empty($value) && in_array($value, $disallowed, true)) {
            $this->addError($field, "Поле {$field} содержит недопустимое значение.");
        }
    }

    /**
     * Проверка на совпадение с другим полем
     */
    private function validateSame(string $field, $value, string $otherField): void
    {
        $otherValue = $this->data[$otherField] ?? null;

        if ($value !== $otherValue) {
            $this->addError($field, "Поле {$field} должно совпадать с полем {$otherField}.");
        }
    }

    /**
     * Проверка на отличие от другого поля
     */
    private function validateDifferent(string $field, $value, string $otherField): void
    {
        $otherValue = $this->data[$otherField] ?? null;

        if ($value === $otherValue) {
            $this->addError($field, "Поле {$field} должно отличаться от поля {$otherField}.");
        }
    }

    /**
     * Добавление ошибки
     */
    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }

        $this->errors[$field][] = $message;
    }

    // ============ СТАТИЧЕСКИЕ МЕТОДЫ ДЛЯ БЫСТРОЙ ПРОВЕРКИ ============

    /**
     * Быстрая проверка email
     */
    public static function isEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Быстрая проверка телефона
     */
    public static function isPhone(string $phone): bool
    {
        $cleanPhone = preg_replace('/[^\d+]/', '', $phone);
        return strlen($cleanPhone) >= 10;
    }

    /**
     * Быстрая проверка пароля (минимальные требования)
     */
    public static function isPasswordStrong(string $password): bool
    {
        // Минимум 8 символов, хотя бы одна цифра и одна буква
        return strlen($password) >= 8 &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[a-zA-Z]/', $password);
    }

    /**
     * Очистка строки от XSS
     */
    public static function clean(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Очистка массива от XSS
     */
    public static function cleanArray(array $data): array
    {
        $cleaned = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $cleaned[$key] = self::cleanArray($value);
            } else {
                $cleaned[$key] = self::clean($value);
            }
        }
        return $cleaned;
    }

    /**
     * Триммирование строк
     */
    public static function trimArray(array $data): array
    {
        $trimmed = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $trimmed[$key] = self::trimArray($value);
            } elseif (is_string($value)) {
                $trimmed[$key] = trim($value);
            } else {
                $trimmed[$key] = $value;
            }
        }
        return $trimmed;
    }
    /**
     * Валидация уникальности в базе данных
     */
    private function validateUnique(string $field, $value, string $table, string $column = null, $excludeId = null): void
    {
        if (empty($value)) {
            return;
        }

        $column = $column ?: $field;
        $db = Database::getInstance();

        // Валидация имен таблиц и столбцов (только буквы, цифры, подчеркивания)
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $table)) {
            $this->addError($field, "Некорректное имя таблицы.");
            return;
        }

        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            $this->addError($field, "Некорректное имя столбца.");
            return;
        }

        // Используем имена напрямую (они уже провалидированы)
        $sql = "SELECT COUNT(*) FROM `{$table}` WHERE `{$column}` = ?";
        $params = [$value];

        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        try {
            $count = $db->value($sql, $params);
            if ($count > 0) {
                $this->addError($field, "Значение поля {$field} уже используется.");
            }
        } catch (\Exception $e) {
            error_log("Unique validation error: " . $e->getMessage());
        }
    }

    /**
     * Валидация по регулярному выражению
     */
    private function validateRegex(string $field, $value, string $pattern, string $message = null): void
    {
        if (!empty($value) && !preg_match($pattern, $value)) {
            $error = $message ?? "Поле {$field} имеет неверный формат.";
            $this->addError($field, $error);
        }
    }

    /**
     * Валидация по callback функции
     */
    private function validateCallback(string $field, $value, callable $callback, string $message = null): void
    {
        if (!empty($value) && !$callback($value)) {
            $error = $message ?? "Поле {$field} содержит недопустимое значение.";
            $this->addError($field, $error);
        }
    }
}