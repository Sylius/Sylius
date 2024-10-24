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

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function isCurrencyDisabled(CurrencyInterface $currency): bool;

    public function isCurrencyEnabled(CurrencyInterface $currency): bool;
}
