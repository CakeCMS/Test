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
use Core\Controller\AppController;
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
     * Remove action value fron request data.
     *
     * @var bool
     */
    protected $_removeDataAction = false;

    /**
     * Disable controller components.
     *
     * @var array
     */
    protected $_unloadComponents = ['Security'];

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
     * Adds additional event spies to the controller/view event manager.
     *
     * @param   \Cake\Event\Event $event
     * @param   AppController|null $controller
     */
    public function controllerSpy($event, $controller = null)
    {
        if ($controller !== null) {
            if (count($this->_unloadComponents)) {
                foreach ($this->_unloadComponents as $componentName) {
                    $controller->components()->unload($componentName);
                }
            }
        }

        parent::controllerSpy($event, $controller);
    }

    /**
     * Calling this method will add a CSRF token to the request.
     *
     * Both the POST data and cookie will be populated when this option
     * is enabled. The default parameter names will be used.
     *
     * @return  $this
     */
    public function enableCsrfToken()
    {
        $this->_csrfToken = true;
        return $this;
    }

    /**
     * Calling this method will re-store flash messages into the test session
     * after being removed by the FlashHelper
     *
     * @return  $this
     */
    public function enableRetainFlashMessages()
    {
        $this->_retainFlashMessages = true;
        return $this;
    }

    /**
     * Calling this method will enable a SecurityComponent
     * compatible token to be added to request data. This
     * lets you easily test actions protected by SecurityComponent.
     *
     * @return  $this
     */
    public function enableSecurityToken()
    {
        $this->_securityToken = true;
        return $this;
    }

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
        unset($this->_url);
    }

    /**
     * Add the CSRF and Security Component tokens if necessary.
     *
     * @param   string $url The URL the form is being submitted on.
     * @param   array $data The request body data.
     * @return  array The request body with tokens added.
     */
    protected function _addTokens($url, $data)
    {
        if ($this->_securityToken === true) {
            $data = $this->_removeActionData($data);
            $keys = array_map(function ($field) {
                return preg_replace('/(\.\d+)+$/', '', $field);
            }, array_keys(Hash::flatten($data)));
            $tokenData = $this->_buildFieldToken($url, array_unique($keys));
            $data['_Token'] = $tokenData;
            $data['_Token']['debug'] = 'SecurityComponent debug data would be added here';
        }

        $data = $this->_setCsrfToken($data);

        return $data;
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
        return TableRegistry::get($this->_corePlugin . '.' . $tableName);
    }

    /**
     * Prepare url.
     *
     * @param   array $url
     * @return  array
     */
    protected function _getUrl(array $url = [])
    {
        return Hash::merge($this->_url, $url);
    }

    /**
     * Remove action value from data.
     *
     * @param   array $data
     * @return  array
     */
    protected function _removeActionData(array $data)
    {
        if ($this->_removeDataAction === true) {
            if (Arr::key('action', $data)) {
                unset($data['action']);
            }
        }

        return $data;
    }

    /**
     * Setup csrf token for data.
     *
     * @param   array $data
     * @return  array
     */
    protected function _setCsrfToken(array $data)
    {
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
}
