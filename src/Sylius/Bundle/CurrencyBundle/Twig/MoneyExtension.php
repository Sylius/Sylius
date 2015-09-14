<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CurrencyBundle\Twig;

use Sylius\Bundle\MoneyBundle\Twig\MoneyExtension as BaseMoneyExtension;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

/**
 * Sylius money Twig helper.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MoneyExtension extends BaseMoneyExtension
{
    /**
     * Currency context.
     *
     * @var CurrencyContextInterface
     */
    protected $currencyContext;

    public function __construct($locale, CurrencyContextInterface $currencyContext)
    {
        parent::__construct($locale, $currencyContext->getCurrency() ?: $currencyContext->getDefaultCurrency());
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCurrency()
    {
        return $this->currencyContext->getCurrency() ?: $this->currencyContext->getDefaultCurrency();
    }
}
