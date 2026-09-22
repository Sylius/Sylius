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

namespace Sylius\Tests\Functional\Doctrine\Mock;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;

class DriverConnectionMock implements Connection
{
    public function prepare(string $sql): Statement
    {
        return new StatementMock();
    }

    public function query(string $sql): Result
    {
        return new DriverResultMock();
    }

    public function quote($value, $type = ParameterType::STRING)
    {
        return (string) $value;
    }

    public function exec(string $sql): int
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function lastInsertId($name = null): string|int|false
    {
        return false;
    }

    public function beginTransaction(): bool
    {
        return true;
    }

    public function commit(): bool
    {
        return true;
    }

    public function rollBack(): bool
    {
        return true;
    }
}
