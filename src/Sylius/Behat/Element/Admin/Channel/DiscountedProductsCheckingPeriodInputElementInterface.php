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

namespace Sylius\Behat\Element\Admin\Channel;

interface DiscountedProductsCheckingPeriodInputElementInterface
{
    public function specifyPeriod(int $period): void;

    public function getPeriod(): int;
}
