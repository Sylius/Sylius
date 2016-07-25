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

use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class MoneyHelper extends Helper implements MoneyHelperInterface
{
    /**
     * @var MoneyHelperInterface
     */
    private $decoratedHelper;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param MoneyHelperInterface $decoratedHelper
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(
        MoneyHelperInterface $decoratedHelper,
        LocaleContextInterface $localeContext
    ) {
        $this->decoratedHelper = $decoratedHelper;
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function formatAmount($amount, $currencyCode = null, $locale = null)
    {
        $locale = $locale ?: $this->localeContext->getLocaleCode();

        return $this->decoratedHelper->formatAmount($amount, $currencyCode, $locale);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_money';
    }
}
