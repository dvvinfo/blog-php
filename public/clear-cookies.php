<?php
// Clear all cookies
foreach ($_COOKIE as $name => $value) {
    setcookie($name, '', time() - 3600, '/');
}

echo "All cookies cleared! <a href='/'>Go to home</a>";
