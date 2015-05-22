<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Templating\Helper;

use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelper as BaseMoneyHelper;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

/**
 * Overrided templating helper to display amounts in currently used currency
 * by default.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MoneyHelper extends BaseMoneyHelper
{
    /**
     * Currency context.
     *
     * @var CurrencyContextInterface
     */
    protected $currencyContext;

    public function __construct($locale, CurrencyContextInterface $currencyContext)
    {
        $this->currencyContext = $currencyContext;

        parent::__construct($locale);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCurrency()
    {
        return $this->currencyContext->getCurrency();
    }
}
