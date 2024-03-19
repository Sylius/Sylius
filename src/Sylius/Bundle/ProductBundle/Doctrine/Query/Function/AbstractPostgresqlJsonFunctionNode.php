<?php

/*
 * This file comes from the `scienta/doctrine-json-functions` package under the MIT license.
 *
 * (c) Scienta
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Doctrine\Query\Function;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Query\SqlWalker;


/**
 * @see https://github.com/ScientaNL/DoctrineJsonFunctions/blob/master/src/Query/AST/Functions/Postgresql/PostgresqlJsonFunctionNode.php
 */
abstract class AbstractPostgresqlJsonFunctionNode extends AbstractJsonFunctionNode
{
    /**
     * @throws Exception
     */
    protected function validatePlatform(SqlWalker$sqlWalker): void
    {
        if (!$sqlWalker->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            throw Exception::notSupported(static::FUNCTION_NAME);
        }
    }

    protected function getSQLFunction(): string
    {
        return strtolower(static::FUNCTION_NAME);
    }
}
