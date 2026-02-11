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

class Dbal4DriverConnectionMock implements Connection
{
    public function prepare(string $sql): Statement
    {
        return new Dbal4StatementMock();
    }

    public function query(string $sql): Result
    {
        return new DriverResultMock();
    }

    public function quote(string $value): string
    {
        return $value;
    }

    public function exec(string $sql): int|string
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function lastInsertId(): int|string
    {
        return 0;
    }

    public function beginTransaction(): void
    {
    }

    public function commit(): void
    {
    }

    public function rollBack(): void
    {
    }

    public function getNativeConnection(): mixed
    {
        throw new \BadMethodCallException('Not implemented');
    }

    public function getServerVersion(): string
    {
        return '3.0.0';
    }
}
