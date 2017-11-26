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
use PHP_CodeSniffer\Util\Tokens;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;

/**
 * Class MethodDeclarationSniff
 *
 * @package Test\Phpcs\CakeCMS\Sniffs\Methods
 */
class MethodDeclarationSniff extends AbstractScopeSniff
{

    /**
     * MethodDeclarationSniff constructor.
     */
    public function __construct()
    {
        parent::__construct([T_CLASS, T_INTERFACE], [T_FUNCTION]);
    }//end __construct()


    /**
     * Processes the function tokens within the class.
     *
     * @param File $phpcsFile The file where this token was found.
     * @param int $stackPtr  The position where the token was found.
     * @param int $currScope The current scope opener token.
     * @return void
     */
    protected function processTokenWithinScope(File $phpcsFile, $stackPtr, $currScope)
    {
        $tokens = $phpcsFile->getTokens();

        $methodName = $phpcsFile->getDeclarationName($stackPtr);
        if ($methodName === null) {
            // Ignore closures.
            return;
        }

        $visibility = 0;
        $static     = 0;
        $abstract   = 0;
        $final      = 0;

        $find   = Tokens::$methodPrefixes;
        $find[] = T_WHITESPACE;
        $prev   = $phpcsFile->findPrevious($find, ($stackPtr - 1), null, true);

        $prefix = $stackPtr;
        while (($prefix = $phpcsFile->findPrevious(Tokens::$methodPrefixes, ($prefix - 1), $prev)) !== false) {
            switch ($tokens[$prefix]['code']) {
            case T_STATIC:
                $static = $prefix;
                break;
            case T_ABSTRACT:
                $abstract = $prefix;
                break;
            case T_FINAL:
                $final = $prefix;
                break;
            default:
                $visibility = $prefix;
                break;
            }
        }

        $fixes = [];

        if ($visibility !== 0 && $final > $visibility) {
            $error = 'The final declaration must precede the visibility declaration';
            $fix   = $phpcsFile->addFixableError($error, $final, 'FinalAfterVisibility');
            if ($fix === true) {
                $fixes[$final]       = '';
                $fixes[($final + 1)] = '';
                if (isset($fixes[$visibility]) === true) {
                    $fixes[$visibility] = 'final '.$fixes[$visibility];
                } else {
                    $fixes[$visibility] = 'final '.$tokens[$visibility]['content'];
                }
            }
        }

        if ($visibility !== 0 && $abstract > $visibility) {
            $error = 'The abstract declaration must precede the visibility declaration';
            $fix   = $phpcsFile->addFixableError($error, $abstract, 'AbstractAfterVisibility');
            if ($fix === true) {
                $fixes[$abstract]       = '';
                $fixes[($abstract + 1)] = '';
                if (isset($fixes[$visibility]) === true) {
                    $fixes[$visibility] = 'abstract '.$fixes[$visibility];
                } else {
                    $fixes[$visibility] = 'abstract '.$tokens[$visibility]['content'];
                }
            }
        }

        if ($static !== 0 && $static < $visibility) {
            $error = 'The static declaration must come after the visibility declaration';
            $fix   = $phpcsFile->addFixableError($error, $static, 'StaticBeforeVisibility');
            if ($fix === true) {
                $fixes[$static]       = '';
                $fixes[($static + 1)] = '';
                if (isset($fixes[$visibility]) === true) {
                    $fixes[$visibility] = $fixes[$visibility].' static';
                } else {
                    $fixes[$visibility] = $tokens[$visibility]['content'].' static';
                }
            }
        }

        // Batch all the fixes together to reduce the possibility of conflicts.
        if (empty($fixes) === false) {
            $phpcsFile->fixer->beginChangeset();
            foreach ($fixes as $stackPtr => $content) {
                $phpcsFile->fixer->replaceToken($stackPtr, $content);
            }

            $phpcsFile->fixer->endChangeset();
        }
    }//end processTokenWithinScope()

    /**
     * Processes a token that is found within the scope that this test is listening to.
     *
     * @param File $phpcsFile The file where this token was found.
     * @param int $stackPtr  The position in the stack where this token was found.
     * @return void
     */
    protected function processTokenOutsideScope(File $phpcsFile, $stackPtr)
    {
    }//end processTokenOutsideScope()
}//end class
