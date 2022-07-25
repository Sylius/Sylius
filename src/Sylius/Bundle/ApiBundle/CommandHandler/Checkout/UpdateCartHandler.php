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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use Sylius\Bundle\ApiBundle\Assigner\OrderPromoCodeAssignerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class UpdateCartHandler implements MessageHandlerInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderAddressModifierInterface $orderAddressModifier,
        private OrderPromoCodeAssignerInterface $orderPromoCodeAssigner,
        private CustomerProviderInterface $customerProvider,
    ) {
    }

    public function __invoke(UpdateCart $updateCart): OrderInterface
    {
        $tokenValue = $updateCart->getOrderTokenValue();

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $tokenValue]);
        Assert::notNull($order, sprintf('Order with %s token has not been found.', $tokenValue));

        if ($updateCart->getEmail()) {
            $order->setCustomer($this->customerProvider->provide($updateCart->getEmail()));
        }

        if ($updateCart->getBillingAddress()) {
            $order = $this->orderAddressModifier->modify(
                $order,
                $updateCart->getBillingAddress(),
                $updateCart->getShippingAddress(),
            );
        }

        $order = $this->orderPromoCodeAssigner->assign($order, $updateCart->getCouponCode());

        return $order;
    }
}
