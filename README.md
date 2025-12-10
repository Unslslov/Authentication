## Установка

### 1. Клонируйте репозиторий:
```bash
git clone https://github.com/Unslslov/Authentication.git
```

### 2. Настройте окружение:
```bash
cp .env.example .env
```

### 3. Добавьте в окружение ключи от Яндекс Капчи:
```bash
YANDEX_CAPTCHA_CLIENT_KEY=
YANDEX_CAPTCHA_SECRET_KEY=
```

### 4. Соберите приложение:
```bash
docker compose up -d --build
```

### 5. Создайте таблицу users:
```bash
docker-compose exec mysql mysql -u root -p

USE database;

CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,name VARCHAR(100) NOT NULL,email VARCHAR(255) NOT NULL UNIQUE,phone VARCHAR(20) NOT NULL UNIQUE,password_hash VARCHAR(255) NOT NULL,created_at DATETIME DEFAULT CURRENT_TIMESTAMP,updated_at DATETIME DEFAULT CURRENT_TIMESTAMP);

exit
```

### 6. Зайдите на localhost:8080:

