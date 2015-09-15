<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class AddProductActionSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $orderItemRepository,
        RepositoryInterface $variantRepository,
        OriginatorInterface $originator
    ) {
        $this->beConstructedWith($orderItemRepository, $variantRepository, $originator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Action\AddProductAction');
    }

    function it_implements_Sylius_promotion_action_interface()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Action\PromotionActionInterface');
    }

    function it_should_add_product_as_promotional(
        $variantRepository,
        $orderItemRepository,
        $originator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $variant,
        PromotionInterface $promotion
    ) {
        $configuration = array('variant' => 500, 'quantity' => 2, 'price' => 0);

        $order->getItems()->willReturn(array());

        $originator->getOrigin($orderItem)->willReturn(null);

        $variantRepository->find($configuration['variant'])->willReturn($variant);

        $orderItemRepository->createNew()->willReturn($orderItem);

        $orderItem->setUnitPrice($configuration['price'])->shouldBeCalled();
        $orderItem->setVariant($variant)->shouldBeCalled();
        $orderItem->setQuantity($configuration['quantity'])->shouldBeCalled();

        $originator->setOrigin($orderItem, $promotion)->shouldBeCalled();

        $order->addItem($orderItem)->shouldBeCalled();

        $this->execute($order, $configuration, $promotion);
    }

    function it_should_not_add_product_if_alredy_exists(
        $originator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        PromotionInterface $promotion
    ) {
        $configuration = array('variant' => 500, 'quantity' => 2, 'price' => 1);

        $order->getItems()->willReturn(array($orderItem));

        $originator->getOrigin($orderItem)->willReturn($promotion);

        $order->addItem($orderItem)->shouldNotBeCalled();

        $this->execute($order, $configuration, $promotion);
    }

    function it_should_remove_promotional_product_during_revert(
        $originator,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        PromotionInterface $promotion
    ) {
        $configuration = array('variant' => 500, 'quantity' => 3, 'price' => 2);

        $order->getItems()->willReturn(array($orderItem));

        $originator->getOrigin($orderItem)->willReturn($promotion);

        $order->removeItem($orderItem)->shouldBeCalled();

        $this->revert($order, $configuration, $promotion);
    }
}
