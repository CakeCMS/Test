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

namespace Test\Suites;

use JBZoo\PHPUnit\Exception;
use Symfony\Component\Finder\Finder;
use JBZoo\PHPUnit\Codestyle as JBCodeStyle;

/**
 * Class CodeStyle
 *
 * @package Test\Suites
 */
class CodeStyle extends JBCodeStyle
{

    /**
     * Package name.
     *
     * @var string
     */
    protected $_packageName = ''; // Overload me!

    /**
     * Package vendor.
     *
     * @var string
     */
    protected $_packageVendor = 'CakeCMS';

    /**
     * Package link.
     *
     * @var string
     */
    protected $_packageLink = 'https://github.com/CakeCMS/_PACKAGE_';

    /**
     * Package copyright.
     *
     * @var string
     */
    protected $_packageCopyright = 'MIT License http://www.opensource.org/licenses/mit-license.php';

    /**
     * Package description.
     *
     * @var array
     */
    protected $_packageDesc = [
        'This file is part of the of the simple cms based on CakePHP 3.',
        'For the full copyright and license information, please view the LICENSE',
        'file that was distributed with this source code.'
    ];

    /**
     * Ignore list for.
     *
     * @var array
     */
    protected $_excludePaths = [
        '.git',
        '.idea',
        'bin',
        'application',
        'bower_components',
        'build',
        'fonts',
        'fixtures',
        'logs',
        'node_modules',
        'resources',
        'vendor',
        'temp',
        'tmp',
        'webroot/css/cache'
    ];

    /**
     * Valid header for PO gettext files.
     *
     * @var array
     */
    protected $_validHeaderPO = [
        '#',
        '# _VENDOR_ _PACKAGE_',
        '#',
        '# _DESCRIPTION_PO_',
        '#',
        '# @package      _PACKAGE_',
        '# @license      _LICENSE_',
        '# @copyright    _COPYRIGHTS_',
        '# @link         _LINK_',
    ];

    /**
     * Valid header for PHP files.
     *
     * @var array
     */
    protected $_validHeaderPHP = [
        '<?php',
        '/**',
        ' * _VENDOR_ _PACKAGE_',
        ' *',
        ' * _DESCRIPTION_PHP_',
        ' *',
        ' * @package     _PACKAGE_',
        ' * @license     _LICENSE_',
        ' * @copyright   _COPYRIGHTS_',
        ' * @link        _LINK_'
    ];

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        parent::setUp();

        //@codeCoverageIgnoreStart
        if (!$this->_packageName) {
            throw new Exception('$this->_packageName is undefined!');
        }
        //@codeCoverageIgnoreEnd

        $this->_replace = [
            '_LINK_'                 => $this->_packageLink,
            '_NAMESPACE_'            => '_VENDOR_\_PACKAGE_',
            '_COPYRIGHTS_'           => $this->_packageCopyright,
            '_PACKAGE_'              => $this->_packageName,
            '_LICENSE_'              => $this->_packageLicense,
            '_AUTHOR_'               => $this->_packageAuthor,
            '_VENDOR_'               => $this->_packageVendor,
            '_DESCRIPTION_PHP_'      => implode($this->_le . ' * ', $this->_packageDesc),
            '_DESCRIPTION_JS_'       => implode($this->_le . ' * ', $this->_packageDesc),
            '_DESCRIPTION_CSS_'      => implode($this->_le . ' * ', $this->_packageDesc),
            '_DESCRIPTION_LESS_'     => implode($this->_le . '// ', $this->_packageDesc),
            '_DESCRIPTION_XML_'      => implode($this->_le . '    ', $this->_packageDesc),
            '_DESCRIPTION_INI_'      => implode($this->_le . '; ', $this->_packageDesc),
            '_DESCRIPTION_SH_'       => implode($this->_le . '# ', $this->_packageDesc),
            '_DESCRIPTION_PO_'       => implode($this->_le . '# ', $this->_packageDesc),
            '_DESCRIPTION_SQL_'      => implode($this->_le . '-- ', $this->_packageDesc),
            '_DESCRIPTION_HTACCESS_' => implode($this->_le . '# ', $this->_packageDesc),
        ];
    }

    /**
     * Try to find cyrilic symbols in the code.
     *
     * @return void
     */
    public function testCyrillic()
    {
        $finder = new Finder();
        $finder
            ->files()
            ->in(PROJECT_ROOT)
            ->exclude($this->_excludePaths)
            ->exclude('tests')
            ->notPath(basename(__FILE__))
            ->notName('/\.md$/')
            ->notName('/\.po$/')
            ->notName('/empty/')
            ->notName('/\.min\.(js|css)$/')
            ->notName('/\.min\.(js|css)\.map$/');

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            $content = \JBZoo\PHPUnit\openFile($file->getPathname());

            if (preg_match('#[А-Яа-яЁё]#ius', $content)) {
                \JBZoo\PHPUnit\fail('File contains cyrilic symbols: ' . $file); // Short message in terminal
            } else {
                \JBZoo\PHPUnit\success();
            }
        }

        \JBZoo\PHPUnit\isTrue(true);
    }

    /**
     * Test copyright headers of PO files.
     *
     * @return void
     */
    public function testHeadersPO()
    {
        $valid = $this->_prepareTemplate(implode($this->_validHeaderPO, $this->_le));

        $finder = new Finder();
        $finder
            ->files()
            ->in(PROJECT_ROOT)
            ->exclude($this->_excludePaths)
            ->name('*.po');

        /** @var \SplFileInfo $file */
        foreach ($finder as $file) {
            $content = \JBZoo\PHPUnit\openFile($file->getPathname());
            \JBZoo\PHPUnit\isContain($valid, $content, false, 'File has no valid header: ' . $file);
        }

        \JBZoo\PHPUnit\isTrue(true);
    }
}
