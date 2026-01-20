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

namespace Sylius\Behat\Context\Setup\Checkout;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Sylius\Behat\Exception\SharedStorageElementNotFoundException;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class ShippingContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private MessageBusInterface $commandBus,
    ) {
    }

    #[Given('I chose :shippingMethod shipping method')]
    #[Given('the customer chose :shippingMethod shipping method')]
    #[Given('the visitor chose :shippingMethod shipping method')]
    public function iChoseShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->chooseShippingMethod($shippingMethod);
    }

    /** @throws SharedStorageElementNotFoundException */
    public function chooseShippingMethod(?ShippingMethodInterface $shippingMethod = null): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $shippingMethodCode = $shippingMethod?->getCode() ?? $this->sharedStorage->get('shipping_method')->getCode();

        $this->commandBus->dispatch(new ChooseShippingMethod(
            $order->getTokenValue(),
            $order->getShipments()->first()->getId(),
            $shippingMethodCode,
        ));
    }
}
