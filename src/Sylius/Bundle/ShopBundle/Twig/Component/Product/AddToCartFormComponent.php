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

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactory;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\Attribute\PreReRender;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PostMount;

#[AsLiveComponent]
class AddToCartFormComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;
    use ComponentToolsTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public Product $product;

    #[LiveProp]
    public ?ProductVariant $variant = null;

    #[LiveProp]
    public string $routeName = 'sylius_shop_cart_summary';

    /** @var array<string, mixed> */
    #[LiveProp]
    public array $routeParameters = [];

    /**
     * @param CartItemFactoryInterface<OrderItem> $cartItemFactory
     * @param class-string $formClass
     */
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly ObjectManager $manager,
        private readonly RouterInterface $router,
        private readonly RequestStack $requestStack,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CartContextInterface $cartContext,
        private readonly AddToCartCommandFactory $addToCartCommandFactory,
        private readonly CartItemFactoryInterface $cartItemFactory,
        private readonly string $formClass,
    ) {
    }

    #[PostMount(priority: 100)]
    public function postMount(): void
    {
        $this->isValidated = true;
    }

    #[PreReRender(priority: -100)]
    public function variantChanged(): void
    {
        $addToCartCommand = $this->getForm()->getData();
        $newVariant = $addToCartCommand->getCartItem()->getVariant();
        if ($newVariant === $this->variant) {
            return;
        }
        $this->variant = $newVariant;

        $this->emitUp('sylius:shop:variant_changed', ['variant' => $this->variant?->getId()]);
    }

    #[LiveAction]
    public function addToCart(): RedirectResponse
    {
        $this->submitForm();
        $addToCartCommand = $this->getForm()->getData();

        $this->eventDispatcher->dispatch(new GenericEvent($addToCartCommand->getCartItem()), SyliusCartEvents::CART_ITEM_ADD);
        $this->manager->flush();

        FlashBagProvider
            ::getFlashBag($this->requestStack)
            ->add('success', 'sylius.cart.add_item');

        return new RedirectResponse($this->router->generate(
            $this->routeName,
            $this->routeParameters,
        ));
    }

    protected function instantiateForm(): FormInterface
    {
        $addToCartCommand = $this->addToCartCommandFactory->createWithCartAndCartItem(
            $this->cartContext->getCart(),
            $this->cartItemFactory->createForProduct($this->product),
        );

        return $this->formFactory->create($this->formClass, $addToCartCommand, ['product' => $this->product]);
    }

    private function getDataModelValue(): string
    {
        return 'debounce(500)|*';
    }
}
