<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Doctrine\Query\Function;

use Scienta\DoctrineJsonFunctions\Query\AST\Functions\Postgresql\PostgresqlJsonFunctionNode;

/**
 * "JSONB_ARRAY_ELEMENTS_TEXT" "(" StringPrimary ")"
 */
final class JsonbArrayElementsText extends PostgresqlJsonFunctionNode
{
    public const FUNCTION_NAME = 'JSONB_ARRAY_ELEMENTS_TEXT';

    /** @var string[] */
    protected $requiredArgumentTypes = [self::STRING_PRIMARY_ARG];

    protected function getSqlForArgs(array $arguments): string
    {
        [$leftArg] = $arguments;
        return sprintf('(SELECT * FROM jsonb_array_elements_text(%s))', $leftArg);
    }
}
