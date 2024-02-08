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

namespace Sylius\Behat\Page\Admin\Currency;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    public function canBeDisabled(): bool;

    public function canHaveExchangeRateChanged(): bool;

    public function changeExchangeRate(string $exchangeRate): void;

    public function getExchangeRateValue(): string;

    public function getCodeDisabledAttribute(): string;
}
