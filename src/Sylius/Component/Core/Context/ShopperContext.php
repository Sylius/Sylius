<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Context;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * Should not be extended, final removed to make this class lazy.
 */
/* final */ class ShopperContext implements ShopperContextInterface
{
    public function __construct(private ChannelContextInterface $channelContext, private CurrencyContextInterface $currencyContext, private LocaleContextInterface $localeContext, private CustomerContextInterface $customerContext)
    {
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channelContext->getChannel();
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyContext->getCurrencyCode();
    }

    public function getLocaleCode(): string
    {
        return $this->localeContext->getLocaleCode();
    }

    public function getCustomer(): ?CustomerInterface
    {
        return $this->customerContext->getCustomer();
    }
}
