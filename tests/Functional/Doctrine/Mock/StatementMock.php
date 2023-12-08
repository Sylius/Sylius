<?php

declare(strict_types=1);

namespace Sylius\Tests\Functional\Doctrine\Mock;

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use EmptyIterator;
use IteratorAggregate;
use Traversable;

class StatementMock implements IteratorAggregate, Statement
{
    public function bindValue($param, $value, $type = null)
    {
    }

    public function bindParam($column, &$variable, $type = null, $length = null)
    {
    }

    public function execute($params = null): Result
    {
        return new DriverResultMock();
    }

    public function getIterator(): Traversable
    {
        return new EmptyIterator();
    }
}
