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

use Core\Plugin;
use Cake\Cache\Cache;
use Cake\Utility\Hash;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase as CakeIntegrationTestCase;

/**
 * Class IntegrationTestCase
 *
 * @package Test\Cases
 */
class IntegrationTestCase extends CakeIntegrationTestCase
{

    /**
     * Default plugin.
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
     * Default url.
     *
     * @var array
     */
    protected $_url = [
        'prefix' => null,
        'plugin' => 'Core',
        'action' => ''
    ];

    /**
     * Setup the test case, backup the static object values so they can be restored.
     * Specifically backs up the contents of Configure and paths in App if they have
     * not already been backed up.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        if (!Plugin::loaded($this->_corePlugin)) {
            $loadParams = [
                'bootstrap' => true,
                'routes'    => true,
                'path'      => ROOT . DS
            ];

            Plugin::load($this->_corePlugin, $loadParams);
            Plugin::routes($this->_corePlugin);
        }

        if ($this->_plugin !== $this->_corePlugin) {
            $options = [
                'bootstrap' => true,
                'routes'    => true,
                'autoload'  => true
            ];

            Plugin::load($this->_plugin, $options);
            Plugin::routes($this->_plugin);

            $this->_url['plugin'] = $this->_plugin;
        }
    }

    /**
     * Clears the state used for requests.
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        Plugin::unload($this->_corePlugin);
        if ($this->_plugin !== $this->_corePlugin) {
            Plugin::unload($this->_plugin);
        }

        Cache::drop('test_cached');
        unset($this->_url);
    }

    /**
     * Prepare url.
     *
     * @param array $url
     * @return array
     */
    protected function _getUrl(array $url = [])
    {
        return Hash::merge($this->_url, $url);
    }

    /**
     * Get table object.
     *
     * @param null|string $name
     * @return \Cake\ORM\Table
     */
    protected function _getTable($name = null)
    {
        $tableName = ($name === null) ? $this->_defaultTable : $name;
        return TableRegistry::get($this->_corePlugin . '.' . $tableName);
    }
}
