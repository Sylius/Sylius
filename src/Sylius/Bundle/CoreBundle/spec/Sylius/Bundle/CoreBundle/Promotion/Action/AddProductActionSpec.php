<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Promotion\Action;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\OrderItemInterface;
use Sylius\Bundle\CoreBundle\Model\VariantInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class AddProductActionSpec extends ObjectBehavior
{
    function let(RepositoryInterface $itemRepository, RepositoryInterface $variantRepository)
    {
        $this->beConstructedWith($itemRepository, $variantRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Promotion\Action\AddProductAction');
    }

    function it_implements_Sylius_promotion_action_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface');
    }

    function it_adds_product_as_promotion(
            RepositoryInterface $itemRepository,
            RepositoryInterface $variantRepository,
            OrderInterface $order,
            OrderItemInterface $item,
            VariantInterface $variant,
            PromotionInterface $promotion)
    {
        $configuration = array('variant' => 500, 'quantity' => 2, 'price' => 0);

        $variantRepository->find($configuration['variant'])->shouldBeCalled()->willReturn($variant);

        $itemRepository->createNew()->willReturn($item);
        $item->setUnitPrice($configuration['price'])->shouldBeCalled()->willReturn($item);
        $item->setVariant($variant)->shouldBeCalled()->willReturn($item);
        $item->setQuantity($configuration['quantity'])->shouldBeCalled()->willReturn($item);

        $order->getItems()->willReturn(array());

        $order->addItem($item)->shouldBeCalled();

        $this->execute($order, $configuration, $promotion);
    }

    function it_does_not_add_product_if_exists(
            RepositoryInterface $variantRepository,
            RepositoryInterface $itemRepository,
            OrderInterface $order,
            OrderItemInterface $item,
            VariantInterface $variant,
            PromotionInterface $promotion)
    {
        $configuration = array('variant' => 500, 'quantity' => 2, 'price' => 1);

        $variantRepository->find($configuration['variant'])->willReturn($variant);

        $itemRepository->createNew()->willReturn($item);
        $item->setUnitPrice($configuration['price'])->shouldBeCalled()->willReturn($item);
        $item->setVariant($variant)->shouldBeCalled()->willReturn($item);
        $item->setQuantity($configuration['quantity'])->shouldBeCalled()->willReturn($item);
        $item->equals($item)->willReturn(true);

        $order->getItems()->willReturn(array($item));

        $order->addItem($item)->shouldNotBeCalled();

        $this->execute($order, $configuration, $promotion);
    }

    function it_reverts_product(
            RepositoryInterface $variantRepository,
            RepositoryInterface $itemRepository,
            OrderInterface $order,
            OrderItemInterface $item,
            VariantInterface $variant,
            PromotionInterface $promotion)
    {
        $configuration = array('variant' => 500, 'quantity' => 3, 'price' => 2);

        $variantRepository->find($configuration['variant'])->willReturn($variant);

        $itemRepository->createNew()->willReturn($item);
        $item->setUnitPrice($configuration['price'])->shouldBeCalled()->willReturn($item);
        $item->setVariant($variant)->shouldBeCalled()->willReturn($item);
        $item->setQuantity($configuration['quantity'])->shouldBeCalled()->willReturn($item);
        $item->equals($item)->willReturn(true);

        $order->getItems()->willReturn(array($item));

        $order->removeItem($item)->shouldBeCalled();

        $this->revert($order, $configuration, $promotion);
    }
}
