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

namespace spec\Sylius\Component\Core\Statistics\Provider\OrdersTotals;

use PhpSpec\ObjectBehavior;
use Webmozart\Assert\Assert;

abstract class AbstractOrdersTotalsProviderSpec extends ObjectBehavior
{
    protected const DATE_FORMAT = '';

    public function __construct()
    {
        Assert::notEmpty(static::DATE_FORMAT, 'The DATE_FORMAT const needs to be set.');
    }

    function getMatchers(): array
    {
        return [
            'beLikeStatisticsCollection' => function ($base, $check): bool {
                if (count($base) !== count($check)) {
                    return false;
                }

                /**
                 * @var int $key
                 * @var \DateTimeImmutable $entryPeriod
                 * @var int $entryTotal
                 */
                foreach ($base as $key => ['period' => $entryPeriod, 'total' => $entryTotal]) {
                    if (
                        $entryTotal !== $check[$key]['total'] ||
                        $entryPeriod->format(static::DATE_FORMAT) !== $check[$key]['period']->format(static::DATE_FORMAT)
                    ) {
                        return false;
                    }
                }

                return true;
            },
        ];
    }
}
