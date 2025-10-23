# Personal Blog

Веб-приложение для ведения личного блога с функционалом регистрации, управления постами, комментариями и аналитикой.

## Технологии

- **Backend**: PHP 8.2+
- **Database**: MySQL 8.0+
- **Frontend**: Tailwind CSS (via CDN)
- **Authentication**: JWT (JSON Web Tokens) с refresh токенами
- **Dependencies**: firebase/php-jwt (via Composer)
- **Deployment**: Docker + Docker Compose

## Быстрый старт с Docker

```bash
# Запустить приложение
docker-compose up -d

# Открыть в браузере
http://localhost:8080

# phpMyAdmin (опционально)
http://localhost:8081
```

Подробнее: [DOCKER.md](DOCKER.md)

## Установка без Docker

1. Клонируйте репозиторий
2. Установите зависимости:
   ```bash
   composer install
   ```
3. Настройте базу данных:
   ```bash
   mysql -u root -p < database/schema.sql
   ```
4. Скопируйте `config/database.example.php` в `config/database.php` и настройте подключение
5. Настройте веб-сервер (Apache/Nginx) с document root в папке `public/`

Подробнее: [INSTALLATION.md](INSTALLATION.md)

## Структура проекта

```
personal-blog/
├── config/              # Конфигурация БД
├── database/            # SQL схемы и миграции
├── public/              # Публичная папка (document root)
│   └── index.php        # Точка входа приложения
├── src/
│   ├── controllers/     # Контроллеры (Auth, Post, Comment, Profile)
│   ├── models/          # Модели данных (User, Post, Comment)
│   ├── middleware/      # Middleware (JWTMiddleware, AuthMiddleware)
│   └── Database.php     # Класс подключения к БД
├── utils/               # Утилиты (JWT, Router, Validator)
├── views/               # Шаблоны (layouts, auth, posts, profile)
├── vendor/              # Composer зависимости
└── composer.json        # Composer конфигурация
```

## Функционал

### Аутентификация

- ✅ Регистрация пользователей
- ✅ Вход/выход из системы
- ✅ JWT авторизация с refresh токенами
- ✅ Автоматическое обновление access token
- ✅ Безопасные httpOnly cookies

### Управление постами

- ✅ Создание постов (только авторизованные)
- ✅ Редактирование своих постов
- ✅ Удаление своих постов
- ✅ Просмотр списка всех постов
- ✅ Просмотр отдельного поста

### Взаимодействие

- ✅ Комментарии к постам (только авторизованные)
- ✅ Лайки и дизлайки (только авторизованные)
- ✅ Счетчик просмотров
- ✅ Отображение автора и даты

### Аналитика

- ✅ Статистика профиля (посты, просмотры, лайки, дизлайки, комментарии)
- ✅ Список своих постов с детальной статистикой
- ✅ Средний рейтинг постов

## JWT Авторизация

Приложение использует современную JWT аутентификацию:

- **Access Token**: 15 минут (для доступа к ресурсам)
- **Refresh Token**: 7 дней (для обновления access token)
- **Хранение**: httpOnly cookies (защита от XSS)
- **База данных**: refresh токены хранятся в БД для возможности отзыва

Подробнее: [JWT_AUTH.md](JWT_AUTH.md)

## Безопасность

### Реализованные меры

- ✅ **SQL Injection**: Prepared statements (PDO)
- ✅ **XSS**: htmlspecialchars() для всего пользовательского контента
- ✅ **Password Security**: password_hash() с bcrypt
- ✅ **JWT Security**: httpOnly, secure, samesite cookies
- ✅ **CSRF Protection**: SameSite=Strict cookies
- ✅ **Input Validation**: Серверная валидация всех данных
- ✅ **Authorization**: Проверка прав доступа через middleware

Подробнее: [SECURITY.md](SECURITY.md)

## Тестирование

План ручного тестирования: [TESTING.md](TESTING.md)

Включает 50+ тестовых сценариев:

- Регистрация и вход
- Управление постами
- Комментарии и лайки
- Аналитика
- Тесты безопасности

## Документация

- [INSTALLATION.md](INSTALLATION.md) - Подробная инструкция по установке
- [DOCKER.md](DOCKER.md) - Запуск через Docker
- [JWT_AUTH.md](JWT_AUTH.md) - Документация по JWT авторизации
- [SECURITY.md](SECURITY.md) - Чеклист безопасности
- [TESTING.md](TESTING.md) - План тестирования
- [QUICK_START.md](QUICK_START.md) - Быстрый старт для тестирования

## Требования

- PHP 8.0+
- MySQL 8.0+
- Composer
- Apache/Nginx с mod_rewrite
- (Опционально) Docker + Docker Compose

## Разработка

### Структура базы данных

```sql
users           # Пользователи
posts           # Посты блога
comments        # Комментарии к постам
refresh_tokens  # JWT refresh токены
```

### Основные классы

- `JWT` - Генерация и проверка JWT токенов
- `JWTMiddleware` - Защита роутов, проверка авторизации
- `Router` - Маршрутизация запросов
- `Validator` - Валидация пользовательского ввода
- `Database` - Подключение к БД через PDO

## Лицензия

MIT License

## Автор

Personal Blog Application - учебный проект для демонстрации:

- MVC архитектуры на PHP
- JWT авторизации с refresh токенами
- Безопасной работы с БД
- Современного UI на Tailwind CSS
