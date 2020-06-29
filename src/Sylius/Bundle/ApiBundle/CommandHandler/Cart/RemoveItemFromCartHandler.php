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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class RemoveItemFromCartHandler implements MessageHandlerInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var OrderProcessorInterface */
    private $orderProcessor;

    /** @var ProductVariantResolverInterface */
    private $variantResolver;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository,
        OrderModifierInterface $orderModifier,
        OrderProcessorInterface $orderProcessor,
        ProductVariantResolverInterface $variantResolver
    ) {
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->orderModifier = $orderModifier;
        $this->orderProcessor = $orderProcessor;
        $this->variantResolver = $variantResolver;
    }

    public function __invoke(RemoveItemFromCart $addItemToCart): OrderInterface
    {
        $product = $this->productRepository->findOneByCode($addItemToCart->productCode);

        Assert::notNull($product);

        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findOneBy([
            'state' => OrderInterface::STATE_CART,
            'tokenValue' => $addItemToCart->tokenValue,
        ]);

        Assert::notNull($cart);

        $orderItemUnits = $cart->getItemUnitsByVariant($this->variantResolver->getVariant($product));

        $this->orderModifier->removeFromOrder($cart, $orderItemUnits->first()->getOrderItem());

        $this->orderProcessor->process($cart);

        return $cart;
    }
}
