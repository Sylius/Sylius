<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\Component\Core\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Context\ShopperContext;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;

final class ShopperContextTest extends TestCase
{
    private ChannelContextInterface&MockObject $channelContext;

    private CurrencyContextInterface&MockObject $currencyContext;

    private LocaleContextInterface&MockObject $localeContext;

    private CustomerContextInterface&MockObject $customerContext;

    private ShopperContext $context;

    protected function setUp(): void
    {
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->currencyContext = $this->createMock(CurrencyContextInterface::class);
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
        $this->customerContext = $this->createMock(CustomerContextInterface::class);
        $this->context = new ShopperContext(
            $this->channelContext,
            $this->currencyContext,
            $this->localeContext,
            $this->customerContext,
        );
    }
//    function let(
//        ChannelContextInterface $channelContext,
//        CurrencyContextInterface $currencyContext,
//        LocaleContextInterface $localeContext,
//        CustomerContextInterface $customerContext,
//    ): void {
//        $this->beConstructedWith($channelContext, $currencyContext, $localeContext, $customerContext);
//    }

    public function testShouldImplementShopperContextInterface(): void
    {
        $this->assertInstanceOf(ShopperContextInterface::class, $this->context);
    }

    public function testShouldGetsCurrentChannelFromContext(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $this->channelContext->expects($this->once())->method('getChannel')->willReturn($channel);

        $this->assertSame($channel, $this->context->getChannel());
    }

    public function testShouldGetsCurrentCurrencyCodeFromContext(): void
    {
        $this->currencyContext->expects($this->once())->method('getCurrencyCode')->willReturn('USD');

        $this->assertSame('USD', $this->context->getCurrencyCode());
    }

    public function testShouldGetsCurrentLocaleCodeFromContextit_gets_a_current_locale_code_from_a_context(): void
    {
        $this->localeContext->expects($this->once())->method('getLocaleCode')->willReturn('en_US');

        $this->assertSame('en_US', $this->context->getLocaleCode());
    }

    public function testShouldGetsCurrentCustomerFromContext(): void
    {
        $customer = $this->createMock(CustomerInterface::class);
        $this->customerContext->expects($this->once())->method('getCustomer')->willReturn($customer);

        $this->assertSame($customer, $this->context->getCustomer());
    }
}
