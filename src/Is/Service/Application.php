<?php
/**
 * This file is part of the Is package
 *
 * (c) Grzegorz Szaliński <grzegorz.szalinski@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Is\Service;

use Silex\Application as BaseApplication;

/**
 * Custom Application class for handling Traits
 *
 * Class Application
 * @package Is\Service
 */
class Application extends BaseApplication
{
    use BaseApplication\UrlGeneratorTrait;
}