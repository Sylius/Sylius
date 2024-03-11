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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssignerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class UpdateCartHandler implements MessageHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderAddressModifierInterface $orderAddressModifier,
        private OrderPromotionCodeAssignerInterface $orderPromotionCodeAssigner,
        private CustomerResolverInterface $customerResolver,
    ) {
    }

    public function __invoke(UpdateCart $updateCart): OrderInterface
    {
        $tokenValue = $updateCart->getOrderTokenValue();

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $tokenValue]);
        Assert::notNull($order, sprintf('Order with %s token has not been found.', $tokenValue));

        if ($updateCart->getEmail()) {
            $order->setCustomer($this->customerResolver->resolve($updateCart->getEmail()));
        }

        $billingAddress = $updateCart->getBillingAddress();
        Assert::nullOrIsInstanceOf($billingAddress, AddressInterface::class);
        $shippingAddress = $updateCart->getShippingAddress();
        Assert::nullOrIsInstanceOf($shippingAddress, AddressInterface::class);
        if ($billingAddress || $shippingAddress) {
            $order = $this->orderAddressModifier->modify($order, $billingAddress, $shippingAddress);
        }

        $order = $this->orderPromotionCodeAssigner->assign($order, $updateCart->getCouponCode());

        return $order;
    }
}
