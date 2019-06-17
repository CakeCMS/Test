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

namespace Test\Cases;

use JBZoo\Utils\Str;
use Cake\Cache\Cache;
use Cake\Routing\Router;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase as CakeTestCase;

/**
 * Class TestCase
 *
 * @package     Test\Cases
 */
class TestCase extends CakeTestCase
{

    /**
     * Default table name.
     *
     * @var     null|string
     */
    protected $_defaultTable;

    /**
     * List of need load plugins.
     *
     * @var     array
     */
    protected $_loadPlugins = [];

    /**
     * Core plugin name.
     *
     * @var     string
     */
    private $_corePlugin = 'Core';

    /**
     * Setup the test case, backup the static object values so they can be restored.
     * Specifically backs up the contents of Configure and paths in App if they have
     * not already been backed up.
     *
     * @return  void
     */
    public function setUp()
    {
        parent::setUp();
        $this->loadPlugins(array_merge($this->_loadPlugins, [$this->_corePlugin]));
    }

    /**
     * Load plugins into a simulated application.
     *
     * Useful to test how plugins being loaded/not loaded interact with other
     * elements in CakePHP or applications.
     *
     * @param   array $plugins List of Plugins to load.
     *
     * @return  \Cake\Http\BaseApplication
     */
    public function loadPlugins(array $plugins = [])
    {
        /** @var \Cake\Http\BaseApplication $app */
        $app = $this->getMockForAbstractClass(
            'TestApp\\Application',
            ['']
        );

        foreach ($plugins as $pluginName => $config) {
            if (is_array($config)) {
                $app->addPlugin($pluginName, $config);
            } else {
                $app->addPlugin($config);
            }
        }

        $app->pluginBootstrap();
        $builder = Router::createRouteBuilder('/');
        $app->pluginRoutes($builder);

        return $app;
    }

    /**
     * Clears the state used for requests.
     *
     * @return  void
     */
    public function tearDown()
    {
        parent::tearDown();
        Cache::drop('test_cached');
    }

    /**
     * Assert check is empty array.
     *
     * @param   array $array
     *
     * @return  void
     */
    public static function assertIsEmptyArray(array $array)
    {
        self::assertSame([], $array);
    }

    /**
     * Check error validation.
     *
     * @param   mixed $field
     * @param   mixed $value
     * @param   array $errorExpected
     *
     * @return  void
     */
    public function assertFieldErrorValidation($field, $value, array $errorExpected = [])
    {
        $data   = [$field => $value];
        $table  = $this->_getTable();
        $entity = $table->newEntity($data);
        $result = $table->save($entity);

        self::assertFalse($result);
        self::assertTrue(is_array($entity->getError($field)));
        self::assertSame($errorExpected, $entity->getError($field));
    }

    /**
     * Get table object.
     *
     * @param   null|string $name
     *
     * @return  \Cake\ORM\Table
     */
    protected function _getTable($name = null)
    {
        return TableRegistry::getTableLocator()->get($name);
    }

    /**
     * Get string from array.
     *
     * @param   string $string
     * @return  array
     */
    protected function _getStrArray($string)
    {
        $output  = [];
        $details = explode("\n", $string);

        foreach ($details as $string) {
            $string   = Str::trim($string);
            $output[] = $string;
        }

        return $output;
    }
}
