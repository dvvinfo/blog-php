# 🧪 Запуск PHPUnit тестов

## Быстрый старт

```bash
# Запустить все тесты
docker exec personal_blog_web vendor/bin/phpunit

# Ожидаемый результат: 61 тест, все зеленые ✅
```

## Что тестируется

### ✅ 61 автоматический тест

**Unit Tests (47 тестов):**

- Validator: 30 тестов (валидация всех полей)
- JWT: 10 тестов (генерация и проверка токенов)
- Router: 4 теста (регистрация роутов)
- User Model: 3 теста (хеширование паролей)

**Feature Tests (14 тестов):**

- Authentication: 6 тестов (полный цикл авторизации)
- Post Validation: 4 теста (валидация постов + XSS)
- Comment Validation: 4 теста (валидация комментариев + XSS)

## Команды

### Все тесты

```bash
docker exec personal_blog_web vendor/bin/phpunit
```

### Только юнит-тесты

```bash
docker exec personal_blog_web vendor/bin/phpunit --testsuite Unit
```

### Только интеграционные тесты

```bash
docker exec personal_blog_web vendor/bin/phpunit --testsuite Feature
```

### Конкретный файл

```bash
docker exec personal_blog_web vendor/bin/phpunit tests/Unit/ValidatorTest.php
```

### С подробным выводом

```bash
docker exec personal_blog_web vendor/bin/phpunit --verbose
```

### С остановкой на первой ошибке

```bash
docker exec personal_blog_web vendor/bin/phpunit --stop-on-failure
```

## Пример вывода

```
PHPUnit 10.5.0 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.29

............................................................ 61 / 61 (100%)

Time: 00:00.234, Memory: 10.00 MB

OK (61 tests, 150 assertions)
```

## Что покрывают тесты

✅ **Безопасность**

- SQL Injection защита (prepared statements)
- XSS защита (htmlspecialchars)
- Password hashing (bcrypt)
- JWT токены

✅ **Валидация**

- Логин (длина, символы)
- Пароль (длина)
- Заголовок поста (длина)
- Содержимое поста (длина)
- Комментарии (длина)

✅ **Аутентификация**

- Генерация JWT токенов
- Проверка JWT токенов
- Хеширование паролей
- Проверка паролей

✅ **Граничные случаи**

- Пустые значения
- Минимальная длина
- Максимальная длина
- Специальные символы

## Troubleshooting

### PHPUnit не найден

```bash
docker exec personal_blog_web composer require --dev phpunit/phpunit
```

### Ошибки автозагрузки

```bash
docker exec personal_blog_web composer dump-autoload
```

### Контейнер не запущен

```bash
docker-compose up -d
docker-compose ps
```

## Добавление своих тестов

1. Создайте файл в `tests/Unit/` или `tests/Feature/`
2. Наследуйтесь от `PHPUnit\Framework\TestCase`
3. Создайте методы начинающиеся с `test`
4. Используйте assertions: `$this->assertEquals()`, `$this->assertTrue()` и т.д.

Пример:

```php
<?php
namespace Tests\Unit;
use PHPUnit\Framework\TestCase;

class MyTest extends TestCase
{
    public function testExample(): void
    {
        $this->assertTrue(true);
    }
}
```

## Полная документация

См. [tests/README.md](tests/README.md) для подробной информации.
