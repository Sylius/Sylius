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
    public function getChannel(): ChannelInterface
    {
        return $this->channelContext->getChannel();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyContext->getCurrencyCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode(): string
    {
        return $this->localeContext->getLocaleCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomer(): ?CustomerInterface
    {
        return $this->customerContext->getCustomer();
    }
}
