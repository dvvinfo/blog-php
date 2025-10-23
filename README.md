# Personal Blog

Веб-приложение для ведения личного блога с функционалом регистрации, управления постами, комментариями и аналитикой.

## Технологии

- PHP 8+
- MySQL 8+
- Tailwind CSS
- Session-based авторизация

## Установка

1. Клонируйте репозиторий
2. Настройте базу данных:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
3. Скопируйте `config/database.example.php` в `config/database.php` и настройте подключение к БД
4. Настройте веб-сервер (Apache/Nginx) с document root в папке `public/`
5. Установите Tailwind CSS (опционально, для кастомизации)

## Структура проекта

```
personal-blog/
├── config/          # Конфигурация
├── database/        # SQL схемы
├── public/          # Публичная папка (document root)
├── src/             # Исходный код
│   ├── controllers/ # Контроллеры
│   ├── models/      # Модели данных
│   └── middleware/  # Middleware
├── utils/           # Утилиты
└── views/           # Шаблоны
```

## Функционал

- Регистрация и авторизация пользователей
- Создание, редактирование и удаление постов
- Комментарии к постам
- Лайки и дизлайки
- Счетчик просмотров
- Аналитика профиля

## Безопасность

- Защита от SQL-инъекций (prepared statements)
- Защита от XSS (htmlspecialchars)
- Безопасное хранение паролей (password_hash)
- Защищенные сессии (httponly, secure, samesite)
