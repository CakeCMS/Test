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

namespace Test\Phpcs\CakeCMS\Sniffs\Classes;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;

/**
 * Class PropertyDeclarationSniff
 *
 * @package Test\Phpcs\Sniffs\Classes
 */
class PropertyDeclarationSniff extends AbstractVariableSniff
{

    /**
     * Processes the function tokens within the class.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $find = Tokens::$scopeModifiers;
        $find = array_merge($find, [T_VARIABLE, T_VAR, T_SEMICOLON]);
        $prev = $phpcsFile->findPrevious($find, ($stackPtr - 1));

        if ($tokens[$prev]['code'] === T_VARIABLE) {
            return;
        }

        if ($tokens[$prev]['code'] === T_VAR) {
            $error = 'The var keyword must not be used to declare a property __';
            $phpcsFile->addError($error, $stackPtr, 'VarUsed');
        }

        $next = $phpcsFile->findNext([T_VARIABLE, T_SEMICOLON], ($stackPtr + 1));
        if ($tokens[$next]['code'] === T_VARIABLE) {
            $error = 'There must not be more than one property declared per statement';
            $phpcsFile->addError($error, $stackPtr, 'Multiple');
        }

        $modifier = $phpcsFile->findPrevious(Tokens::$scopeModifiers, $stackPtr);
        if (($modifier === false) || ($tokens[$modifier]['line'] !== $tokens[$stackPtr]['line'])) {
            $error = 'Visibility must be declared on property "%s"';
            $data  = [$tokens[$stackPtr]['content']];
            $phpcsFile->addError($error, $stackPtr, 'ScopeMissing', $data);
        }

    }//end processMemberVar()

    /**
     * Processes normal variables.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    protected function processVariable(File $phpcsFile, $stackPtr)
    {
        /*
            We don't care about normal variables.
        */
    }//end processVariable()

    /**
     * Processes variables in double quoted strings.
     *
     * @param File $phpcsFile
     * @param int $stackPtr
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr)
    {
        /*
            We don't care about normal variables.
        */
    }//end processVariableInString()
}//end class
