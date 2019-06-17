<?php
/**
 * CakeCMS Test
 *
 * This file is part of the of the simple cms based on CakePHP 3.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package     Test
 * @license     MIT
 * @copyright   MIT License http://www.opensource.org/licenses/mit-license.php
 * @link        https://github.com/CakeCMS/Test".
 * @author      Sergey Kalistratov <kalistratov.s.m@gmail.com>
 */

namespace Test\App;

use Core\Application as CoreApplication;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * Class Application
 *
 * @package Test\App
 */
class Application extends CoreApplication
{

    /**
     * Load all the application configuration and bootstrap logic.
     *
     * Override this method to add additional bootstrap logic for your application.
     *
     * @return  void
     */
    public function bootstrap()
    {
        parent::bootstrap();
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param   \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return  \Cake\Http\MiddlewareQueue The updated middleware queue.
     */
    public function middleware($middleware)
    {
        $middleware->add(new RoutingMiddleware());
        $middleware->add(function ($req, $res, $next) {
            $res = $next($req, $res);
            return $res->withHeader('X-Middleware', 'true');
        });

        return $middleware;
    }
}
