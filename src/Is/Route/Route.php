<?php
/**
 * This file is part of the Is package
 *
 * (c) Grzegorz Szaliñski <grzegorz.szalinski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Is\Route;

use Silex\Route as BaseRoute;

class Route extends BaseRoute
{
    use BaseRoute\SecurityTrait;
}