<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Distributor;

use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class IntegerDistributor implements IntegerDistributorInterface
{
    /**
     * {@inheritdoc}
     */
    public function distribute(float $amount, int $numberOfTargets): array
    {
        Assert::true((1 <= $numberOfTargets), 'Number of targets must be bigger than 0.');

        $sign = $amount < 0 ? -1 : 1;
        $amount = abs($amount);

        $low = (int) ($amount / $numberOfTargets);
        $high = $low + 1;

        $remainder = $amount % $numberOfTargets;
        $result = [];

        for ($i = 0; $i < $remainder; ++$i) {
            $result[] = $high * $sign;
        }

        for ($i = $remainder; $i < $numberOfTargets; ++$i) {
            $result[] = $low * $sign;
        }

        return $result;
    }
}
