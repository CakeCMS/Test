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

namespace Test\Phpcs\CakeCMS\Sniffs\Methods;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\CamelCapsFunctionNameSniff as GenericCamelCapsFunctionNameSniff;

/**
 * Class CamelCapsMethodNameSniff
 *
 * @package Test\Phpcs\CakeCMS\Sniffs\Methods
 */
class CamelCapsMethodNameSniff extends GenericCamelCapsFunctionNameSniff
{

    /**
     * CamelCapsMethodNameSniff constructor.
     */
    public function __construct()
    {
        parent::__construct([T_CLASS, T_INTERFACE, T_TRAIT], [T_FUNCTION], true);
    }//end __construct()


    /**
     * Processes the tokens within the scope.
     *
     * @param File $phpcsFile The file being processed.
     * @param int $stackPtr The position where this token was found.
     * @param int $currScope The position of the current scope.
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null) {
            // Ignore closures.
            return;
        }

        // Ignore magic methods.
        if (preg_match('|^__|', $methodName) !== 0) {
            $magicPart = strtolower(substr($methodName, 2));
            if (isset($this->magicMethods[$magicPart]) === true
                || isset($this->methodsDoubleUnderscore[$magicPart]) === true
            ) {
                return;
            }
        }

        $testName = ltrim($methodName, '_');
        if ($methodName !== '_' && Common::isCamelCaps($testName, false, true, false) === false) {
            $error     = 'Method name "%s" is not in camel caps format';
            $className = $phpcsFile->getDeclarationName($currScope);
            $errorData = [$className . '::' . $methodName];
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $errorData);
            $phpcsFile->recordMetric($stackPtr, 'CamelCase method name', 'no');
        } else {
            $phpcsFile->recordMetric($stackPtr, 'CamelCase method name', 'yes');
        }

    }//end processTokenWithinScope()

    /**
     * Processes the tokens outside the scope.
     *
     * @param File $phpcsFile The file being processed.
     * @param int $stackPtr  The position where this token was found.
     * @return void
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
    }//end processTokenOutsideScope()
}//end class
