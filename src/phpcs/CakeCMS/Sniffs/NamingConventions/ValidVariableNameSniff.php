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

namespace Test\Phpcs\CakeCMS\Sniffs\NamingConventions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;

/**
 * Class ValidVariableNameSniff
 *
 * @package Test\Phpcs\CakeCMS\Sniffs\NamingConventions
 */
class ValidVariableNameSniff extends AbstractVariableSniff
{

    /**
     * Processes class member variables.
     *
     * @param File $phpcsFile The file being scanned.
     * @param int $stackPtr  The position of the current token in the stack passed in $tokens.
     * @return void
     */
    protected function processMemberVar(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $memberProps = $phpcsFile->getMemberProperties($stackPtr);
        if (empty($memberProps) === true) {
            return;
        }

        $memberName     = ltrim($tokens[$stackPtr]['content'], '$');
        $scope          = $memberProps['scope'];
        $scopeSpecified = $memberProps['scope_specified'];

        if ($memberProps['scope'] === 'private' || $memberProps['scope'] === 'protected') { // Fixed by SmetDenis
            $isPublic = false;
        } else {
            $isPublic = true;
        }

        // If it's a private member, it must have an underscore on the front.
        if ($isPublic === false && $memberName{0} !== '_') {
            $error = ucfirst($memberProps['scope']) . ' member variable "%s" must be prefixed with an underscore'; // Fixed by SmetDenis
            $data  = [$memberName];
            $phpcsFile->addError($error, $stackPtr, 'PrivateNoUnderscore', $data);
            return;
        }

        // If it's not a private member, it must not have an underscore on the front.
        if ($isPublic === true && $scopeSpecified === true && $memberName{0} === '_') {
            $error = '%s member variable "%s" must not be prefixed with an underscore';
            $data  = [
                ucfirst($scope),
                $memberName
            ];

            $phpcsFile->addError($error, $stackPtr, 'PublicUnderscore', $data);
            return;
        }

    }//end processMemberVar()

    /**
     * Processes normal variables.
     *
     * @param File $phpcsFile The file where this token was found.
     * @param int $stackPtr  The position where the token was found.
     * @return void
     */
    protected function processVariable(File $phpcsFile, $stackPtr)
    {
    }//end processVariable()

    /**
     * Processes variables in double quoted strings.
     *
     * @param File $phpcsFile The file where this token was found.
     * @param int $stackPtr  The position where the token was found.
     * @return void
     */
    protected function processVariableInString(File $phpcsFile, $stackPtr)
    {
    }//end processVariableInString()
}//end class
