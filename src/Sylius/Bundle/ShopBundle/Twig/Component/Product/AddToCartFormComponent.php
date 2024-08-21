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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product;

use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactory;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class AddToCartFormComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    #[LiveProp(updateFromParent: false)]
    public Product $product;

    #[LiveProp(updateFromParent: false)]
    public ?OrderItem $orderItem = null;

    /**
     * @param class-string $formClass
     * @param CartItemFactoryInterface<OrderItem> $cartItemFactory
     */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly CartItemFactoryInterface $cartItemFactory,
        private readonly AddToCartCommandFactory $addToCartCommandFactory,
        private readonly CartContextInterface $cartContext,
        private readonly OrderItemQuantityModifierInterface $quantityModifier,
        private readonly string $formClass,
    ) {
    }

    #[PreReRender(priority: -100)]
    public function variantChanged(): void
    {
        $variant = $this->orderItem->getVariant();

        if (!$variant->isEnabled()) {
            $variant = null;
        }

        $this->emitUp('sylius:shop:variant_changed', ['variant' => $variant?->getId()]);
    }

    protected function instantiateForm(): FormInterface
    {
        $cart = $this->cartContext->getCart();

        if ($this->orderItem === null) {
            /** @var OrderItem $orderItem */
            $orderItem = $this->cartItemFactory->createForProduct($this->product);
            $this->orderItem = $orderItem;

            $this->quantityModifier->modify($this->orderItem, 1);
        }

        $addToCartCommand = $this->addToCartCommandFactory->createWithCartAndCartItem($cart, $this->orderItem);

        return $this->formFactory->create($this->formClass, $addToCartCommand, ['product' => $this->product]);
    }
}
