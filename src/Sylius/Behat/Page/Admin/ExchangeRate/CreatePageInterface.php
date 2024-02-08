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

namespace Sylius\Behat\Page\Admin\ExchangeRate;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePage;

interface CreatePageInterface extends BaseCreatePage
{
    public function specifyRatio(string $ratio): void;

    public function chooseSourceCurrency(string $currency): void;

    public function chooseTargetCurrency(string $currency): void;

    public function hasFormValidationError(string $expectedMessage): bool;
}
