<?php
return [
    'yandex' => [
        'client_key' => getenv('YANDEX_CAPTCHA_CLIENT_KEY'),
        'secret_key' => getenv('YANDEX_CAPTCHA_SECRET_KEY'),
        'enabled' => true,
    ],
];
?>