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

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Query\SqlWalker;

abstract class AbstractPostgresqlJsonFunctionNode extends AbstractJsonFunctionNode
{
    /**
     * @param SqlWalker $sqlWalker
     * @throws Exception
     */
    protected function validatePlatform(SqlWalker$sqlWalker): void
    {
        if (!$sqlWalker->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            throw Exception::notSupported(static::FUNCTION_NAME);
        }
    }

    /**
     * @return string
     */
    protected function getSQLFunction(): string
    {
        return strtolower(static::FUNCTION_NAME);
    }
}
