<?php

namespace App\Utils;

abstract class FormRequest
{
    protected array $data = [];
    protected array $errors = [];
    protected array $validatedData = [];
    protected bool $authorized = false;

    /**
     * Конструктор
     */
    public function __construct()
    {
        $this->data = $this->getRequestData();
        $this->authorized = $this->authorize();
    }

    /**
     * Получение данных запроса
     */
    protected function getRequestData(): array
    {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $data = $_GET;
        }

        // Для других методов (PUT, PATCH, DELETE)
        if (empty($data)) {
            parse_str(file_get_contents('php://input'), $data);
        }

        // Очистка от XSS
        return Validator::cleanArray($data);
    }

    /**
     * Проверка авторизации для запроса
     * Переопределяется в дочерних классах
     */
    protected function authorize(): bool
    {
        return true;
    }

    /**
     * Правила валидации
     * Должны быть определены в дочерних классах
     */
    abstract protected function rules(): array;

    /**
     * Сообщения об ошибках (опционально)
     */
    protected function messages(): array
    {
        return [];
    }

    /**
     * Кастомные названия полей (опционально)
     */
    protected function attributes(): array
    {
        return [];
    }

    /**
     * Валидация данных
     */
    public function validate(): bool
    {
        if (!$this->authorized) {
            $this->errors['auth'] = ['У вас нет прав для выполнения этого действия.'];
            return false;
        }

        $validator = new Validator();

        $validator->validate($this->data, $this->rules());

        if ($validator->fails()) {
            $this->errors = $validator->errors();
            return false;
        }

        $this->validatedData = $this->prepareValidatedData($validator->validated());
        return true;
    }

    /**
     * Подготовка валидированных данных
     */
    protected function prepareValidatedData(array $data): array
    {
        return $data;
    }

    /**
     * Проверка наличия ошибок
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
     * Получение всех ошибок в виде строки
     */
    public function errorsToString(): string
    {
        $messages = [];

        foreach ($this->errors as $field => $fieldErrors) {
            $fieldName = $this->attributes()[$field] ?? $field;
            foreach ($fieldErrors as $error) {
                $messages[] = str_replace($field, $fieldName, $error);
            }
        }

        return implode("\n", $messages);
    }

    /**
     * Получение валидированных данных
     */
    public function validated(): array
    {
        return $this->validatedData;
    }

    /**
     * Получение конкретного валидированного поля
     */
    public function input(string $key, $default = null)
    {
        return $this->validatedData[$key] ?? $this->data[$key] ?? $default;
    }

    /**
     * Получение всех данных (включая невалидированные)
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Получение файлов (если есть)
     */
    public function files(): array
    {
        return $_FILES ?? [];
    }

    /**
     * Проверка наличия поля
     */
    public function has(string $key): bool
    {
        return isset($this->data[$key]);
    }

    /**
     * Заполнение данных (например, для тестирования)
     */
    public function fill(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Магический метод для получения данных
     */
    public function __get($name)
    {
        return $this->input($name);
    }

    /**
     * Статический метод для быстрого создания
     */
    public static function make(): self
    {
        return new static();
    }
}
?>