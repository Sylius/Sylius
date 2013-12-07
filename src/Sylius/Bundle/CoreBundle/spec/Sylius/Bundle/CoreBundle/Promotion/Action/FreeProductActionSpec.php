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

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FreeProductActionSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $itemRepository
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $variantRepository
     */
    function let($itemRepository, $variantRepository)
    {
        $this->beConstructedWith($itemRepository, $variantRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Promotion\Action\FreeProductAction');
    }

    function it_implements_Sylius_promotion_action_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface           $order
     * @param Sylius\Bundle\CoreBundle\Model\OrderItemInterface       $item
     * @param Sylius\Bundle\CoreBundle\Model\VariantInterface         $variant
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     */
    function it_adds_free_product_as_promotion($itemRepository, $variantRepository, $order, $item, $variant, $promotion)
    {
        $configuration = array('variant' => 500, 'quantity' => 2);

        $variantRepository->find($configuration['variant'])->shouldBeCalled()->willReturn($variant);

        $itemRepository->createNew()->willReturn($item);
        $item->setUnitPrice(0)->shouldBeCalled();
        $item->setVariant($variant)->shouldBeCalled();
        $item->setQuantity($configuration['quantity'])->shouldBeCalled();

        $order->getItems()->willReturn(array());
        $order->addItem($item)->shouldBeCalled();

        $this->execute($order, $configuration, $promotion);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface           $order
     * @param Sylius\Bundle\CoreBundle\Model\OrderItemInterface       $item
     * @param Sylius\Bundle\CoreBundle\Model\VariantInterface         $variant
     * @param Sylius\Bundle\PromotionsBundle\Model\PromotionInterface $promotion
     */
    function it_does_not_add_product_if_exist($variantRepository, $order, $item, $variant, $promotion)
    {
        $configuration = array('variant' => 500, 'quantity' => 2);

        $variantRepository->find($configuration['variant'])->willReturn($variant);

        $item->getUnitPrice()->willReturn(0);
        $item->getVariant()->willReturn($variant);

        $order->getItems()->willReturn(array($item));
        $order->addItem($item)->shouldNotBeCalled();

        $this->execute($order, $configuration, $promotion);
    }
}
