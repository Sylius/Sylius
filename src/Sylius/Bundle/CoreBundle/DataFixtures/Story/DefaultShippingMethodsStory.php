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

namespace Sylius\Bundle\CoreBundle\DataFixtures\Story;

use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ChannelFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\PaymentMethodFactoryInterface;
use Sylius\Bundle\CoreBundle\DataFixtures\Factory\ShippingMethodFactoryInterface;
use Zenstruck\Foundry\Story;

final class DefaultShippingMethodsStory extends Story implements DefaultShippingMethodsStoryInterface
{
    public function __construct(private ShippingMethodFactoryInterface $shippingMethodFactory)
    {
    }

    public function build(): void
    {
        $this->shippingMethodFactory::new()
            ->withName('UPS')
            ->withCode('ups')
            ->withChannels(['FASHION_WEB'])
            ->create()
        ;

        $this->shippingMethodFactory::new()
            ->withName('DHL Express')
            ->withCode('dhl_express')
            ->withChannels(['FASHION_WEB'])
            ->create()
        ;

        $this->shippingMethodFactory::new()
            ->withName('FedEx')
            ->withCode('fedex')
            ->withChannels(['FASHION_WEB'])
            ->create()
        ;
    }
}
