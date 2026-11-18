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

namespace Sylius\Bundle\ShopBundle\Controller;

use Sylius\Bundle\ShopBundle\Form\Type\CartType;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * @experimental
 */
final readonly class CartSummaryAction
{
    public function __construct(
        private Environment $twig,
        private CartContextInterface $cartContext,
        private OrderRepositoryInterface $orderRepository,
        private FormFactoryInterface $formFactory,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(Request $request, ?string $formType = null, ?string $template = null): Response
    {
        $template ??= '@SyliusShop/cart/index.html.twig';
        $formType ??= CartType::class;

        $cart = $this->cartContext->getCart();

        if (null !== $cart->getId()) {
            $cart = $this->orderRepository->findCartForSummary($cart->getId());
        }

        $form = $this->formFactory->create($formType, $cart);

        $this->eventDispatcher->dispatch(new GenericEvent($cart), SyliusCartEvents::CART_SUMMARY);

        return new Response($this->twig->render($template, [
            'cart' => $cart,
            'form' => $form->createView(),
        ]));
    }
}
