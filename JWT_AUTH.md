# JWT Authentication Documentation

## Обзор

Приложение использует JWT (JSON Web Tokens) для аутентификации с механизмом refresh токенов.

## Архитектура

### Типы токенов

1. **Access Token**
   - Срок жизни: 15 минут
   - Хранится в httpOnly cookie
   - Используется для доступа к защищенным ресурсам
   - Содержит: user_id, login, type, iat, exp

2. **Refresh Token**
   - Срок жизни: 7 дней
   - Хранится в httpOnly cookie и базе данных
   - Используется для получения нового access token
   - Содержит: user_id, type, jti (unique ID), iat, exp

### Процесс аутентификации

```
1. Пользователь вводит логин/пароль
   ↓
2. Сервер проверяет credentials
   ↓
3. Генерируются access и refresh токены
   ↓
4. Токены сохраняются в httpOnly cookies
   ↓
5. Refresh token сохраняется в БД
```

### Процесс обновления токена

```
1. Access token истек
   ↓
2. Middleware проверяет refresh token
   ↓
3. Если refresh token валиден и есть в БД
   ↓
4. Генерируется новый access token
   ↓
5. Новый access token отправляется в cookie
```

### Процесс выхода

```
1. Пользователь нажимает "Выход"
   ↓
2. Refresh token удаляется из БД
   ↓
3. Оба cookie удаляются
   ↓
4. Редирект на страницу входа
```

## Компоненты

### 1. JWT Utility (`utils/JWT.php`)

Основной класс для работы с JWT токенами.

**Методы:**
- `generateAccessToken($userId, $login)` - генерация access token
- `generateRefreshToken($userId)` - генерация refresh token
- `verifyToken($token)` - проверка и декодирование токена
- `revokeRefreshToken($token)` - отзыв refresh token
- `revokeAllUserTokens($userId)` - отзыв всех токенов пользователя
- `cleanExpiredTokens()` - очистка истекших токенов

### 2. JWT Middleware (`src/middleware/JWTMiddleware.php`)

Middleware для защиты роутов.

**Методы:**
- `requireAuth()` - требует валидный access token
- `requireGuest()` - требует отсутствие токена
- `requirePostOwner($postId)` - требует владельца поста
- `isAuthenticated()` - проверка аутентификации
- `getUserId()` - получение ID пользователя
- `getUserLogin()` - получение логина пользователя

### 3. Refresh Tokens Table

```sql
CREATE TABLE refresh_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Безопасность

### Реализованные меры

1. **httpOnly Cookies**
   - Токены недоступны через JavaScript
   - Защита от XSS атак

2. **Secure Flag**
   - Токены передаются только по HTTPS (в production)

3. **SameSite=Strict**
   - Защита от CSRF атак

4. **Хранение Refresh Token в БД**
   - Возможность отзыва токенов
   - Контроль активных сессий

5. **Короткий срок жизни Access Token**
   - Минимизация риска при компрометации

6. **Секретный ключ**
   - Минимум 256 бит
   - Должен храниться в переменных окружения

### Рекомендации для Production

1. **Изменить секретный ключ**
   ```php
   // В utils/JWT.php
   private static string $secretKey = getenv('JWT_SECRET_KEY');
   ```

2. **Использовать HTTPS**
   - Обязательно для secure cookies

3. **Настроить переменные окружения**
   ```env
   JWT_SECRET_KEY=your-very-long-secret-key-min-256-bits
   JWT_ACCESS_EXPIRATION=900
   JWT_REFRESH_EXPIRATION=604800
   ```

4. **Регулярная очистка истекших токенов**
   ```php
   // Добавить в cron
   JWT::cleanExpiredTokens();
   ```

5. **Мониторинг активных сессий**
   - Отслеживание количества refresh токенов на пользователя
   - Ограничение количества одновременных сессий

## API Endpoints

### Аутентификация

**POST /register**
- Регистрация нового пользователя
- Возвращает: access_token и refresh_token в cookies

**POST /login**
- Вход в систему
- Возвращает: access_token и refresh_token в cookies

**GET /logout**
- Выход из системы
- Удаляет токены из cookies и БД

### Защищенные роуты

Все роуты, требующие аутентификации:
- `/posts/create` - создание поста
- `/posts/{id}/edit` - редактирование поста
- `/posts/{id}/delete` - удаление поста
- `/posts/{id}/like` - лайк
- `/posts/{id}/dislike` - дизлайк
- `/posts/{id}/comments` - добавление комментария
- `/profile/analytics` - аналитика

## Отладка

### Проверка токена

```php
$token = JWT::getTokenFromCookie('access_token');
$payload = JWT::verifyToken($token);
var_dump($payload);
```

### Просмотр активных refresh токенов

```sql
SELECT * FROM refresh_tokens WHERE user_id = 1;
```

### Отзыв всех токенов пользователя

```php
JWT::revokeAllUserTokens($userId);
```

## Миграция с Session на JWT

Основные изменения:

1. **Session::start()** → удалено (не нужно)
2. **Session::isAuthenticated()** → `JWTMiddleware::isAuthenticated()`
3. **Session::getUserId()** → `JWTMiddleware::getUserId()`
4. **Session::get('user_login')** → `JWTMiddleware::getUserLogin()`
5. **AuthMiddleware** → `JWTMiddleware`

## Преимущества JWT

1. **Stateless** - не требует хранения сессий на сервере
2. **Масштабируемость** - легко масштабировать горизонтально
3. **API-ready** - готово для использования в API
4. **Безопасность** - современный стандарт аутентификации
5. **Контроль** - возможность отзыва токенов

## Недостатки и решения

1. **Размер токена**
   - Решение: минимизировать payload, использовать короткие ключи

2. **Невозможность отзыва access token**
   - Решение: короткий срок жизни (15 минут)

3. **Сложность**
   - Решение: хорошая документация и middleware

## Тестирование

### Тест регистрации
1. Зарегистрироваться
2. Проверить cookies (access_token, refresh_token)
3. Проверить запись в refresh_tokens

### Тест обновления токена
1. Подождать 15 минут (или изменить expiration)
2. Обновить страницу
3. Проверить что access_token обновился

### Тест выхода
1. Выйти из системы
2. Проверить что cookies удалены
3. Проверить что refresh_token удален из БД

## Troubleshooting

### Токен не работает
- Проверить срок действия
- Проверить секретный ключ
- Проверить формат токена

### Refresh token не обновляет access token
- Проверить наличие в БД
- Проверить срок действия
- Проверить логи ошибок

### Постоянный редирект на /login
- Проверить cookies
- Проверить middleware
- Проверить логи Apache/PHP
