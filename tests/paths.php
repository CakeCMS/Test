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

use Cake\Filesystem\Folder;

/**
 * Use the DS to separate the directories in other defines
 */
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * The full path to the directory which holds "src", WITHOUT a trailing DS.
 */
define('ROOT', dirname(__DIR__));

/**
 * The actual directory name for the application directory. Normally named 'src'.
 */
define('APP_DIR', 'src');

/**
 * The full path to the test directory.
 */
define('TESTS_DIR', ROOT . DS . 'tests' . DS);

/**
 * The full path to the test app directory.
 */
define('TEST_APP_DIR', TESTS_DIR . 'App' . DS);

/**
 * The full path to application dir.
 */
define('APP_ROOT', TEST_APP_DIR);

/**
 * Path to the application's directory.
 */
define('APP', TEST_APP_DIR . APP_DIR . DS);

/**
 * The full path to the config directory.
 */
define('CONFIG', TEST_APP_DIR . 'config' . DS);

/**
 * File path to the webroot directory.
 */
define('WWW_ROOT', TEST_APP_DIR . 'webroot' . DS);

/**
 * Path to the temporary files directory.
 */
define('TMP', TEST_APP_DIR . 'tmp' . DS);

/**
 * Path to the logs directory.
 */
define('LOGS', TEST_APP_DIR . 'logs' . DS);

/**
 * Path to the cache files directory. It can be shared between hosts in a multi-server setup.
 */
define('CACHE', TEST_APP_DIR . 'cache' . DS);

/**
 * The absolute path to the "cake" directory, WITHOUT a trailing DS.
 *
 * CakePHP should always be installed with composer, so look there.
 */
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');

/**
 * Path to the cake directory.
 */
define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
define('CAKE', CORE_PATH . 'src' . DS);

//  Create test app folders.
$folder = new Folder();
$folder->create(LOGS, 0777);
$folder->create(CACHE, 0777);