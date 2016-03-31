<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Currency;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @param CurrencyInterface $currency
     *
     * @return bool
     */
    public function isCurrencyDisabled(CurrencyInterface $currency);

    /**
     * @param CurrencyInterface $currency
     *
     * @return bool
     */
    public function isCurrencyEnabled(CurrencyInterface $currency);
}
