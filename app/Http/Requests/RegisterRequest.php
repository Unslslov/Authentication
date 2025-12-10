<?php

namespace App\Http\Requests;

use App\Utils\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Правила валидации
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'min:2', 'max:100'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'regex:/^[\d\s\-\+\(\)]+$/'],
            'password' => ['required', 'min:6', 'confirmed'],
            'password_confirmation' => ['required']
        ];
    }

    /**
     * Сообщения об ошибках
     */
    protected function messages(): array
    {
        return [
            'name.required' => 'Имя обязательно для заполнения',
            'email.email' => 'Введите корректный email адрес',
            'phone.regex' => 'Введите корректный номер телефона',
            'password.min' => 'Пароль должен быть минимум 6 символов',
            'password.confirmed' => 'Пароли не совпадают'
        ];
    }

    /**
     * Названия полей
     */
    protected function attributes(): array
    {
        return [
            'name' => 'Имя',
            'email' => 'Email',
            'phone' => 'Телефон',
            'password' => 'Пароль',
            'password_confirmation' => 'Подтверждение пароля'
        ];
    }
}