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

namespace spec\Sylius\Bundle\CoreBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerTaxCategoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Provider\CustomerTaxCategoryProviderInterface;

final class ChannelBasedDefaultCustomerTaxCategoryProviderSpec extends ObjectBehavior
{
    function it_implements_customer_tax_category_provider_interface(): void
    {
        $this->shouldImplement(CustomerTaxCategoryProviderInterface::class);
    }

    function it_provides_a_default_customer_tax_category_from_a_channel_of_an_order(
        ChannelInterface $channel,
        OrderInterface $order,
        CustomerTaxCategoryInterface $defaultCustomerTaxCategory
    ): void {
        $order->getChannel()->willReturn($channel);
        $channel->getDefaultCustomerTaxCategory()->willReturn($defaultCustomerTaxCategory);

        $this->getCustomerTaxCategory($order)->shouldReturn($defaultCustomerTaxCategory);
    }
}
