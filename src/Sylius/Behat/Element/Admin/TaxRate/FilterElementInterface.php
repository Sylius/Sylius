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

namespace Sylius\Behat\Element\Admin\TaxRate;

interface FilterElementInterface
{
    public function specifyDateFrom(string $dateType, string $date): void;

    public function specifyDateTo(string $dateType, string $date): void;

    public function filter(): void;
}
