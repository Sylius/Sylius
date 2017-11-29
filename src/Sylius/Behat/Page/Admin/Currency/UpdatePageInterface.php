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

namespace Sylius\Behat\Page\Admin\Currency;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    /**
     * @return bool
     */
    public function canBeDisabled(): bool;

    /**
     * @return bool
     */
    public function canHaveExchangeRateChanged(): bool;

    /**
     * @param string $exchangeRate
     */
    public function changeExchangeRate(string $exchangeRate): void;

    /**
     * @return string
     */
    public function getExchangeRateValue(): string;

    /**
     * @return string
     */
    public function getCodeDisabledAttribute(): string;
}
