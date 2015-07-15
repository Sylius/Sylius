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

use Sylius\Bundle\CurrencyBundle\Templating\Helper\MoneyHelper as BaseMoneyHelper;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

class MoneyHelper extends BaseMoneyHelper
{
    /**
     * @var LocaleContextInterface
     */
    protected $localeContext;

    /**
     * @param LocaleContextInterface   $localeContext   The locale context
     * @param CurrencyContextInterface $currencyContext The currency context
     */
    public function __construct(LocaleContextInterface $localeContext, CurrencyContextInterface $currencyContext)
    {
        $this->localeContext = $localeContext;

        parent::__construct($this->getDefaultLocale(), $currencyContext);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultLocale()
    {
        return $this->localeContext->getLocale();
    }
}
