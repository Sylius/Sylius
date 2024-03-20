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

namespace Sylius\Bundle\ProductBundle\Doctrine\ORM\Query\AST\Function;

/**
 * "JSONB_ARRAY_ELEMENTS_TEXT" "(" StringPrimary ")"
 */
final class JsonbArrayElementsText extends AbstractPostgresqlJsonFunctionNode
{
    public const FUNCTION_NAME = 'JSONB_ARRAY_ELEMENTS_TEXT';

    /** @var string[] */
    protected array $requiredArgumentTypes = [self::STRING_PRIMARY_ARG];

    protected function getSqlForArgs(array $arguments): string
    {
        return sprintf('(SELECT * FROM jsonb_array_elements_text(%s))', array_shift($arguments));
    }
}
