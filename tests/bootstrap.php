<?php

/**
 * PHPUnit Bootstrap File
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load application files
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/Router.php';
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/models/User.php';
require_once __DIR__ . '/../src/models/Post.php';
require_once __DIR__ . '/../src/models/Comment.php';
