<?php

namespace App\Http\Requests;

use App\Utils\Config;
use App\Utils\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Правила валидации
     */
    protected function rules(): array
    {
        return [
            'login' => ['required'],
            'password' => ['required'],
            'smart-token' => ['required']
        ];
    }

    /**
     * Сообщения об ошибках
     */
    protected function messages(): array
    {
        return [
            'smart-token.required' => 'Пройдите проверку капчи'
        ];
    }

    /**
     * Валидация капчи Яндекс
     */
    public function validate(): bool
    {
        if (!parent::validate()) {
            return false;
        }

        // Валидация Яндекс SmartCaptcha
        if (!$this->validateYandexCaptcha()) {
            $this->errors['smart-token'] = ['Проверка капчи не пройдена'];
            return false;
        }

        return true;
    }

    /**
     * Проверка Яндекс SmartCaptcha
     */
    private function validateYandexCaptcha(): bool
    {
        $token = $this->input('smart-token');

        if (empty($token)) {
            return false;
        }

        // Получаем ключи из конфигурации
        $config = Config::get('captcha');
        $secretKey = $config['yandex']['secret_key'];

        // Если капча отключена в конфигурации
        if (!$config['yandex']['enabled']) {
            return true;
        }

        $url = "https://smartcaptcha.yandexcloud.net/validate";
        $params = [
            'secret' => $secretKey,
            'token' => $token,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log("Yandex Captcha API error: HTTP $httpCode");
            return false;
        }

        $result = json_decode($response, true);

        return isset($result['status']) && $result['status'] === 'ok';
    }
}