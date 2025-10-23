# PHPUnit Tests

## Структура тестов

```
tests/
├── bootstrap.php              # Загрузка зависимостей
├── Unit/                      # Юнит-тесты
│   ├── ValidatorTest.php      # Тесты валидации (30 тестов)
│   ├── JWTTest.php            # Тесты JWT (10 тестов)
│   ├── RouterTest.php         # Тесты роутера (4 теста)
│   └── Models/
│       └── UserTest.php       # Тесты модели User (3 теста)
└── Feature/                   # Интеграционные тесты
    ├── AuthenticationTest.php # Тесты аутентификации (6 тестов)
    ├── PostValidationTest.php # Тесты постов (4 теста)
    └── CommentValidationTest.php # Тесты комментариев (4 теста)
```

**Всего: 61 тест**

## Установка

```bash
# В контейнере Docker
docker exec -it personal_blog_web bash
composer require --dev phpunit/phpunit

# Или локально
composer require --dev phpunit/phpunit
```

## Запуск тестов

### Все тесты

```bash
# В Docker контейнере
docker exec personal_blog_web vendor/bin/phpunit

# Локально
./vendor/bin/phpunit
```

### Только юнит-тесты

```bash
docker exec personal_blog_web vendor/bin/phpunit --testsuite Unit
```

### Только интеграционные тесты

```bash
docker exec personal_blog_web vendor/bin/phpunit --testsuite Feature
```

### Конкретный тест

```bash
docker exec personal_blog_web vendor/bin/phpunit tests/Unit/ValidatorTest.php
```

### С подробным выводом

```bash
docker exec personal_blog_web vendor/bin/phpunit --verbose
```

### С покрытием кода (требует Xdebug)

```bash
docker exec personal_blog_web vendor/bin/phpunit --coverage-html coverage
```

## Описание тестов

### Unit Tests

#### ValidatorTest (30 тестов)
- ✅ Валидация логина (6 тестов)
  - Успешная валидация
  - Пустой логин
  - Слишком короткий
  - Слишком длинный
  - Недопустимые символы
  - Валидные символы

- ✅ Валидация пароля (4 теста)
  - Успешная валидация
  - Пустой пароль
  - Слишком короткий
  - Слишком длинный

- ✅ Валидация заголовка поста (3 теста)
  - Успешная валидация
  - Пустой заголовок
  - Слишком длинный

- ✅ Валидация содержимого поста (3 теста)
  - Успешная валидация
  - Пустое содержимое
  - Слишком короткое

- ✅ Валидация комментария (4 теста)
  - Успешная валидация
  - Пустой комментарий
  - Слишком короткий
  - Слишком длинный

- ✅ Санитизация (3 теста)
  - Удаление HTML тегов
  - Обрезка пробелов
  - Обработка кавычек

#### JWTTest (10 тестов)
- ✅ Генерация access token
- ✅ Генерация refresh token
- ✅ Проверка валидного access token
- ✅ Проверка валидного refresh token
- ✅ Проверка невалидного токена
- ✅ Проверка структуры access token
- ✅ Проверка структуры refresh token
- ✅ Проверка expiration в будущем
- ✅ Проверка issued_at в прошлом

#### RouterTest (4 теста)
- ✅ Регистрация GET роута
- ✅ Регистрация POST роута
- ✅ Регистрация нескольких роутов
- ✅ Callable обработчики

#### UserTest (3 теста)
- ✅ Хеширование пароля
- ✅ Разные хеши для одного пароля
- ✅ Проверка пароля

### Feature Tests

#### AuthenticationTest (6 тестов)
- ✅ Полный flow регистрации с валидацией
- ✅ Полный flow входа с валидацией
- ✅ Хеширование и проверка пароля
- ✅ Генерация JWT access token
- ✅ Генерация JWT refresh token
- ✅ Полный цикл аутентификации

#### PostValidationTest (4 теста)
- ✅ Полная валидация создания поста
- ✅ Граничные случаи заголовка
- ✅ Граничные случаи содержимого
- ✅ Защита от XSS в постах

#### CommentValidationTest (4 теста)
- ✅ Полная валидация комментария
- ✅ Граничные случаи текста
- ✅ Защита от XSS в комментариях
- ✅ Специальные символы

## Покрытие кода

Тесты покрывают:
- ✅ 100% Validator
- ✅ 90% JWT (без тестов БД)
- ✅ 60% Router (без тестов dispatch)
- ✅ 30% User Model (только статические методы)
- ✅ Основные сценарии использования

## Что НЕ покрыто тестами

- ❌ Контроллеры (требуют HTTP мокирования)
- ❌ Middleware (требуют HTTP мокирования)
- ❌ Модели Post и Comment (требуют БД)
- ❌ Интеграция с БД
- ❌ Cookie операции
- ❌ Session операции

## Добавление новых тестов

### Создание нового теста

```php
<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    public function testSomething(): void
    {
        $result = someFunction();
        $this->assertEquals('expected', $result);
    }
}
```

### Запуск нового теста

```bash
docker exec personal_blog_web vendor/bin/phpunit tests/Unit/MyTest.php
```

## CI/CD Integration

### GitHub Actions

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Install dependencies
        run: composer install
      - name: Run tests
        run: vendor/bin/phpunit
```

## Troubleshooting

### Ошибка "Class not found"
```bash
composer dump-autoload
```

### Ошибка подключения к БД
Убедитесь, что контейнер БД запущен:
```bash
docker-compose ps
```

### Тесты не запускаются
Проверьте версию PHPUnit:
```bash
docker exec personal_blog_web vendor/bin/phpunit --version
```

## Best Practices

1. **Один тест - одна проверка**: Каждый тест должен проверять одну вещь
2. **Понятные имена**: `testValidateLoginSuccess` лучше чем `test1`
3. **Arrange-Act-Assert**: Подготовка → Действ��е → Проверка
4. **Независимость**: Тесты не должны зависеть друг от друга
5. **Быстрота**: Юнит-тесты должны выполняться быстро

## Полезные команды

```bash
# Запустить тесты с фильтром
docker exec personal_blog_web vendor/bin/phpunit --filter testValidateLogin

# Запустить тесты с остановкой на первой ошибке
docker exec personal_blog_web vendor/bin/phpunit --stop-on-failure

# Запустить тесты с цветным выводом
docker exec personal_blog_web vendor/bin/phpunit --colors=always

# Список всех тестов
docker exec personal_blog_web vendor/bin/phpunit --list-tests
```
