<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\User\Context\CustomerContextInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ShopperContext implements ShopperContextInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var CurrencyContextInterface
     */
    private $currencyContext;

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var CustomerContextInterface
     */
    private $customerContext;

    /**
     * @param ChannelContextInterface $channelContext
     * @param CurrencyContextInterface $currencyContext
     * @param LocaleContextInterface $localeContext
     * @param CustomerContextInterface $customerContext
     */
    public function __construct(
        ChannelContextInterface $channelContext,
        CurrencyContextInterface $currencyContext,
        LocaleContextInterface $localeContext,
        CustomerContextInterface $customerContext
    ) {
        $this->channelContext = $channelContext;
        $this->currencyContext = $currencyContext;
        $this->localeContext = $localeContext;
        $this->customerContext = $customerContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getChannel()
    {
        return $this->channelContext->getChannel();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode()
    {
        return $this->currencyContext->getCurrencyCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode()
    {
        return $this->localeContext->getCurrentLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer()
    {
        return $this->customerContext->getCustomer();
    }
}
