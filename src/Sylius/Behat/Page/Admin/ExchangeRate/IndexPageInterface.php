<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ExchangeRate;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @param string $currencyName
     */
    public function chooseCurrencyFilter($currencyName);
}
