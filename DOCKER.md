# Запуск через Docker

## Требования

- Docker Desktop установлен и запущен
- Порты 8080, 8081, 3306 свободны

## Быстрый старт

### 1. Запустить контейнеры

```bash
docker-compose up -d
```

Это запустит:
- **Web сервер** (Apache + PHP 8.2) на порту 8080
- **MySQL 8.0** на порту 3306
- **phpMyAdmin** на порту 8081

### 2. Дождаться инициализации

Первый запуск может занять 1-2 минуты (скачивание образов и инициализация БД).

Проверить статус:
```bash
docker-compose ps
```

Все сервисы должны быть в статусе "Up".

### 3. Открыть приложение

- **Блог**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081 (root / root)

## Полезные команды

### Просмотр логов
```bash
# Все сервисы
docker-compose logs -f

# Только web сервер
docker-compose logs -f web

# Только база данных
docker-compose logs -f db
```

### Остановить контейнеры
```bash
docker-compose stop
```

### Запустить снова
```bash
docker-compose start
```

### Полностью удалить (с данными)
```bash
docker-compose down -v
```

### Перезапустить после изменений в коде
```bash
docker-compose restart web
```

### Войти в контейнер
```bash
# Web сервер
docker exec -it personal_blog_web bash

# База данных
docker exec -it personal_blog_db bash
```

## Структура Docker

### Сервисы

1. **web** (PHP 8.2 + Apache)
   - Порт: 8080
   - Document root: `/var/www/html/public`
   - Установлены: PDO, PDO_MySQL
   - Включен: mod_rewrite

2. **db** (MySQL 8.0)
   - Порт: 3306
   - База: personal_blog
   - Пользователь: blog_user
   - Пароль: blog_password
   - Root пароль: root

3. **phpmyadmin**
   - Порт: 8081
   - Для управления БД через веб-интерфейс

### Volumes

- `db_data` - постоянное хранилище данных MySQL
- Код приложения монтируется напрямую (изменения видны сразу)

## Подключение к БД

Приложение автоматически подключается к БД через настройки в `config/database.php`.

Если нужно подключиться извне (например, через MySQL Workbench):
- Host: localhost
- Port: 3306
- User: blog_user
- Password: blog_password
- Database: personal_blog

## Устранение проблем

### Порты заняты

Если порты 8080, 8081 или 3306 заняты, измените их в `docker-compose.yml`:

```yaml
ports:
  - "9090:80"  # Вместо 8080
```

### База данных не инициализируется

```bash
# Удалить и пересоздать
docker-compose down -v
docker-compose up -d
```

### Ошибки прав доступа

```bash
# В контейнере web
docker exec -it personal_blog_web bash
chown -R www-data:www-data /var/www/html
```

### Проверить подключение к БД

```bash
docker exec -it personal_blog_web bash
php -r "try { new PDO('mysql:host=db;dbname=personal_blog', 'blog_user', 'blog_password'); echo 'OK'; } catch(Exception \$e) { echo \$e->getMessage(); }"
```

## Тестирование

После запуска следуйте инструкциям в `TESTING.md`:

1. Откройте http://localhost:8080
2. Зарегистрируйте пользователя
3. Создайте посты
4. Протестируйте функционал

## Остановка и очистка

```bash
# Остановить без удаления данных
docker-compose stop

# Удалить контейнеры, но сохранить данные БД
docker-compose down

# Удалить всё включая данные
docker-compose down -v

# Удалить образы
docker-compose down --rmi all
```
