<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Util;

/*
 * Borrowed from symfony/routing. Thank you!
 *
 * Copyright (c) 2004-2018 Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class FqcnFinder
{
    /**
     * Returns the fully-qualified class name for the first class in the file.
     *
     * @param string $file A PHP file path
     *
     * @return string
     *
     * @throws \InvalidArgumentException if invalid PHP code
     * @throws \RuntimeException         if FQCN could not be determined
     */
    public static function findClass(string $file): string
    {
        $class = false;
        $namespace = false;
        $tokens = \token_get_all(\file_get_contents($file));

        if (1 === \count($tokens) && T_INLINE_HTML === $tokens[0][0]) {
            throw new \InvalidArgumentException(\sprintf('The file "%s" does not contain PHP code. Did you forgot to add the "<?php" start tag at the beginning of the file?', $file));
        }

        for ($i = 0; isset($tokens[$i]); ++$i) {
            $token = $tokens[$i];

            if ( ! isset($token[1])) {
                continue;
            }

            if (true === $class && T_STRING === $token[0]) {
                return $namespace.'\\'.$token[1];
            }

            if (true === $namespace && T_STRING === $token[0]) {
                $namespace = $token[1];
                while (isset($tokens[++$i][1]) && \in_array($tokens[$i][0], [T_NS_SEPARATOR, T_STRING], true)) {
                    $namespace .= $tokens[$i][1];
                }
                $token = $tokens[$i];
            }

            if (T_CLASS === $token[0]) {
                // Skip usage of ::class constant and anonymous classes
                $skipClassToken = false;
                for ($j = $i - 1; $j > 0; --$j) {
                    if ( ! isset($tokens[$j][1])) {
                        break;
                    }

                    if (T_DOUBLE_COLON === $tokens[$j][0] || T_NEW === $tokens[$j][0]) {
                        $skipClassToken = true;
                        break;
                    }

                    if ( ! \in_array($tokens[$j][0], [T_WHITESPACE, T_DOC_COMMENT, T_COMMENT], true)) {
                        break;
                    }
                }

                if ( ! $skipClassToken) {
                    $class = true;
                }
            }

            if (T_NAMESPACE === $token[0]) {
                $namespace = true;
            }
        }

        throw new \RuntimeException('Could not determine a fully-qualified class name for file '.$file);
    }
}
