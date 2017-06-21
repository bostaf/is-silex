<?php
/**
 * This file is part of the Is package
 *
 * (c) Grzegorz Szaliński <grzegorz.szalinski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

setlocale(LC_ALL, 'pl_PL');
require_once __DIR__.'/../vendor/autoload.php';

$app = new Is\Application\Application();

// include bootstrap
require __DIR__.'/../src/app.php';

$app->run();
