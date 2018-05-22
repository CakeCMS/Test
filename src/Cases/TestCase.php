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

use Core\Cms;
use Core\Plugin;
use JBZoo\Utils\Str;
use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase as CakeTestCase;

/**
 * Class TestCase
 *
 * @package Test\Cases
 */
class TestCase extends CakeTestCase
{

    /**
     * Default plugin name.
     *
     * @var string
     */
    protected $_plugin = 'Core';

    /**
     * Core plugin.
     *
     * @var string
     */
    protected $_corePlugin = 'Core';

    /**
     * Default table name.
     *
     * @var null|string
     */
    protected $_defaultTable;

    /**
     * Hold CMS object.
     *
     * @var Cms
     */
    protected $_cms;

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

        if ($this->_plugin !== $this->_corePlugin) {
            $options = [
                'bootstrap' => true,
                'routes'    => true,
                'autoload'  => true
            ];

            Plugin::load($this->_plugin, $options);
            Plugin::routes($this->_plugin);
        }

        if (!Plugin::loaded($this->_corePlugin)) {
            $loadParams = [
                'bootstrap' => true,
                'routes'    => true,
                'path'      => ROOT . DS
            ];

            Plugin::load($this->_corePlugin, $loadParams);
            Plugin::routes($this->_corePlugin);
        }

        $this->_cms = Cms::getInstance();
    }

    /**
     * Clears the state used for requests.
     *
     * @return  void
     */
    public function tearDown()
    {
        parent::tearDown();
        Plugin::unload($this->_corePlugin);
        if ($this->_plugin !== $this->_corePlugin) {
            Plugin::unload($this->_plugin);
        }
        Cache::drop('test_cached');
    }

    /**
     * Assert check is empty array.
     *
     * @param   array $array
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
     * @return  \Cake\ORM\Table
     */
    protected function _getTable($name = null)
    {
        $tableName = ($name === null) ? $this->_defaultTable : $name;
        return TableRegistry::getTableLocator()->get($this->_corePlugin . '.' . $tableName);
    }

    /**
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
