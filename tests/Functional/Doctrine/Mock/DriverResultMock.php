<?php

declare(strict_types=1);

namespace Sylius\Tests\Functional\Doctrine\Mock;

use BadMethodCallException;
use Doctrine\DBAL\Driver\Result;

/**
 * This class is mostly copied from {@link https://github.com/doctrine/orm/blob/2.14.x/tests/Doctrine/Tests/Mocks/DriverResultMock.php}
 */
class DriverResultMock implements Result
{
    private array $resultSet;

    /**
     * Creates a new mock statement that will serve the provided fake result set to clients.
     *
     * @param array $resultSet The faked SQL result set.
     */
    public function __construct(array $resultSet = [])
    {
        $this->resultSet = $resultSet;
    }

    public function fetchNumeric()
    {
        $row = $this->fetchAssociative();

        return $row === false ? false : array_values($row);
    }

    public function fetchAssociative()
    {
        $current = current($this->resultSet);
        next($this->resultSet);

        return $current;
    }

    public function fetchOne()
    {
        $row = $this->fetchNumeric();

        return $row ? $row[0] : false;
    }

    public function fetchAllNumeric(): array
    {
        $values = [];
        while (($row = $this->fetchNumeric()) !== false) {
            $values[] = $row;
        }

        return $values;
    }

    public function fetchAllAssociative(): array
    {
        $resultSet = $this->resultSet;
        reset($resultSet);

        return $resultSet;
    }

    public function fetchFirstColumn(): array
    {
        throw new BadMethodCallException('Not implemented');
    }

    public function rowCount(): int
    {
        return 0;
    }

    public function columnCount(): int
    {
        $resultSet = $this->resultSet;

        return count(reset($resultSet) ?: []);
    }

    public function free(): void
    {
    }
}
