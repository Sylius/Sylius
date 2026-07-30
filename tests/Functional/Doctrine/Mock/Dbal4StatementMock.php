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

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;

class Dbal4StatementMock implements Statement
{
    public function bindValue(int|string $param, mixed $value, ParameterType $type = ParameterType::STRING): void
    {
    }

    public function execute(): Result
    {
        return new DriverResultMock();
    }
}
