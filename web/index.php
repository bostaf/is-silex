<?php
setlocale(LC_ALL, 'pl_PL');
require_once __DIR__.'/../vendor/autoload.php';

$app = new Is\Service\Application();

require __DIR__.'/../src/app.php';

$app->run();
