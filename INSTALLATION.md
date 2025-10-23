# Инструкция по установке и запуску

## Требования

- PHP 8.0 или выше
- MySQL 8.0 или выше
- Apache с mod_rewrite (или Nginx)
- Веб-сервер с поддержкой .htaccess

## Установка

### 1. Настройка базы данных

Создайте базу данных MySQL:

```sql
CREATE DATABASE personal_blog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Импортируйте схему:

```bash
mysql -u root -p personal_blog < database/schema.sql
```

### 2. Настройка подключения к БД

Отредактируйте файл `config/database.php` и укажите ваши данные:

```php
return [
    'host' => 'localhost',
    'port' => '3306',
    'database' => 'personal_blog',
    'username' => 'your_username',
    'password' => 'your_password',
    'charset' => 'utf8mb4',
    // ...
];
```

### 3. Настройка веб-сервера

#### Apache

Убедитесь, что mod_rewrite включен:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Настройте виртуальный хост с document root в папке `public/`:

```apache
<VirtualHost *:80>
    ServerName blog.local
    DocumentRoot /path/to/personal-blog/public
    
    <Directory /path/to/personal-blog/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Nginx

Пример конфигурации:

```nginx
server {
    listen 80;
    server_name blog.local;
    root /path/to/personal-blog/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4. Права доступа

Убедитесь, что веб-сервер имеет права на чтение файлов:

```bash
sudo chown -R www-data:www-data /path/to/personal-blog
sudo chmod -R 755 /path/to/personal-blog
```

### 5. Запуск

Откройте браузер и перейдите по адресу:
- `http://blog.local` (если настроили виртуальный хост)
- `http://localhost/personal-blog/public` (если используете localhost)

## Быстрый запуск для разработки

Если у вас установлен PHP CLI, можно запустить встроенный сервер:

```bash
cd public
php -S localhost:8000
```

Затем откройте `http://localhost:8000` в браузере.

## Первые шаги

1. Зарегистрируйте нового пользователя через `/register`
2. Войдите в систему через `/login`
3. Создайте первый пост через кнопку "Создать пост"
4. Просмотрите аналитику в разделе "Мой профиль"

## Структура URL

- `/` - главная страница со списком постов
- `/register` - регистрация
- `/login` - вход
- `/logout` - выход
- `/posts/create` - создание поста
- `/posts/{id}` - просмотр поста
- `/posts/{id}/edit` - редактирование поста
- `/profile/analytics` - аналитика профиля

## Устранение неполадок

### Ошибка подключения к БД

Проверьте:
- Правильность данных в `config/database.php`
- Что MySQL сервер запущен
- Что база данных создана и схема импортирована

### 404 ошибки на всех страницах

Проверьте:
- Что mod_rewrite включен (Apache)
- Что файл `.htaccess` существует в папке `public/`
- Что AllowOverride установлен в All

### Ошибки сессии

Проверьте:
- Права на запись в папку сессий PHP
- Настройки session в php.ini

## Безопасность для продакшена

Перед развертыванием на продакшене:

1. Отключите отображение ошибок в `public/index.php`:
```php
error_reporting(0);
ini_set('display_errors', 0);
```

2. Используйте HTTPS и обновите настройки сессии
3. Храните пароли БД в переменных окружения
4. Настройте регулярные бэкапы базы данных
5. Добавьте rate limiting для защиты от брутфорса

## Поддержка

При возникновении проблем проверьте:
- Логи веб-сервера (Apache: `/var/log/apache2/error.log`)
- Логи PHP (обычно в `/var/log/php/error.log`)
- Логи приложения (если настроены)
