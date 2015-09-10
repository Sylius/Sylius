<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CurrencyBundle\Twig\MoneyExtension as BaseMoneyExtension;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

class MoneyExtension extends BaseMoneyExtension
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
        parent::__construct($localeContext->getCurrentLocale(), $currencyContext);

        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultLocale()
    {
        return $this->localeContext->getCurrentLocale();
    }
}
