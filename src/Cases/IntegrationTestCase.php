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
use JBZoo\Utils\Arr;
use Cake\Cache\Cache;
use Cake\Utility\Text;
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
     * Default plugin.
     *
     * @var string
     */
    protected $_plugin = 'Core';

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
     * Add the CSRF and Security Component tokens if necessary.
     *
     * @param string $url The URL the form is being submitted on.
     * @param array $data The request body data.
     * @return array The request body with tokens added.
     */
    protected function _addTokens($url, $data)
    {
        if ($this->_securityToken === true) {
            if (Arr::key('action', $data)) {
                unset($data['action']);
            }

            $keys = array_map(function ($field) {
                return preg_replace('/(\.\d+)+$/', '', $field);
            }, array_keys(Hash::flatten($data)));
            $tokenData = $this->_buildFieldToken($url, array_unique($keys));
            $data['_Token'] = $tokenData;
            $data['_Token']['debug'] = 'SecurityComponent debug data would be added here';
        }

        if ($this->_csrfToken === true) {
            if (!Arr::key('csrfToken', $this->_cookie)) {
                $this->_cookie['csrfToken'] = Text::uuid();
            }

            if (!Arr::key('_csrfToken', $data)) {
                $data['_csrfToken'] = $this->_cookie['csrfToken'];
            }
        }

        return $data;
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
}
