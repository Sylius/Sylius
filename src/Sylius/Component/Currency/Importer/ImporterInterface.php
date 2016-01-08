<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Importer;

use Sylius\Component\Currency\Model\CurrencyInterface;

interface ImporterInterface
{
    /**
     * @param array $options
     */
    public function configure(array $options = []);

    /**
     * @param CurrencyInterface[] $managedCurrencies
     */
    public function import(array $managedCurrencies = []);
}
