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

namespace Sylius\Behat\Page\Admin\TaxRate;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyCode(string $code): void;

    public function nameIt(string $name): void;

    public function specifyAmount(string $amount): void;

    public function specifyStartDate(\DateTimeInterface $startDate): void;

    public function specifyEndDate(\DateTimeInterface $endDate): void;

    public function chooseZone(string $name): void;

    public function chooseCategory(string $name): void;

    public function chooseCalculator(string $name): void;

    public function chooseIncludedInPrice(): void;
}
