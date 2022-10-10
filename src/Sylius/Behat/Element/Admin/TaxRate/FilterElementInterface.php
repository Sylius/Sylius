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

namespace Sylius\Behat\Element\Admin\TaxRate;

interface FilterElementInterface
{
    public function specifyStartDateFrom(string $date): void;

    public function specifyStartDateTo(string $date): void;

    public function specifyEndDateFrom(string $date): void;

    public function specifyEndDateTo(string $date): void;

    public function filter(): void;
}
