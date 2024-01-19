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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraints\Compound;

/** @experimental */
final class DatePeriod extends Compound
{
    /**
     * @param array<mixed> $options
     */
    protected function getConstraints(array $options): array
    {
        return [
            new EndDateIsNotBeforeStartDate(),
            new EndDateAgainstInterval(),
        ];
    }
}
