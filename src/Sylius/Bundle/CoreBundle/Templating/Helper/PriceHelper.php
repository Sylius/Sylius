<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Templating\Helper;

use Sylius\Bundle\MoneyBundle\Templating\Helper\PriceHelperInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PriceHelper extends Helper implements PriceHelperInterface
{
    /**
     * @var PriceHelperInterface
     */
    private $decoratedHelper;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @param PriceHelperInterface $decoratedHelper
     * @param CurrencyContextInterface $currencyContext
     */
    public function __construct(
        PriceHelperInterface $decoratedHelper,
        CurrencyContextInterface $currencyContext
    ) {
        $this->decoratedHelper = $decoratedHelper;
        $this->currencyContext = $currencyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function convertAndFormatAmount($amount, $currencyCode = null, $locale = null)
    {
        if (null === $currencyCode) {
            $currency = $this->currencyContext->getCurrency();

            if (null !== $currency) {
                $currencyCode = $currency->getCode();
            }
        }

        return $this->decoratedHelper->convertAndFormatAmount($amount, $currencyCode, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_price';
    }
}
