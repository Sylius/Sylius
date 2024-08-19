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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product\AddToCart;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactory;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class FormComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;

    #[LiveProp]
    public Product $product;

    #[LiveProp]
    public OrderItem $orderItem;

    public AddToCartCommandInterface $addToCartCommand;

    /**
     * @param class-string $formClass
     * @param FactoryInterface<OrderItem> $orderItemFactory
     */

    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly FactoryInterface $orderItemFactory,
        private readonly AddToCartCommandFactory $addToCartCommandFactory,
        private readonly CartContextInterface $cartContext,
        private readonly OrderItemQuantityModifierInterface $quantityModifier,
        private readonly string $formClass,
    ) {
    }

    #[LiveAction]
    public function variantChanged(): void
    {
        if (is_array($this->formValues['cartItem']['variant'])) {
            $selectedVariantCode = array_values($this->formValues['cartItem']['variant'])[0];
        } else {
            $selectedVariantCode = $this->formValues['cartItem']['variant'];
        }

        $this->emit('variantChanged', ['productVariantCode' => $selectedVariantCode]);
    }

    protected function instantiateForm(): FormInterface
    {
        $this->orderItem = $this->orderItemFactory->createNew();
        $cart = $this->cartContext->getCart();

        $this->addToCartCommand = $this->addToCartCommandFactory->createWithCartAndCartItem($cart, $this->orderItem);
        $this->quantityModifier->modify($this->orderItem, 1);

        return $this->formFactory->create($this->formClass, $this->addToCartCommand, ['product' => $this->product]);
    }
}
