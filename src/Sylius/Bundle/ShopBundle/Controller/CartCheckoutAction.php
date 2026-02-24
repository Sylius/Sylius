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

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\OrderBundle\Resetter\CartChangesResetterInterface;
use Sylius\Bundle\ShopBundle\Form\Type\CartType;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final readonly class CartCheckoutAction
{
    public function __construct(
        private Environment $twig,
        private CartContextInterface $cartContext,
        private FormFactoryInterface $formFactory,
        private EventDispatcherInterface $eventDispatcher,
        private RouterInterface $router,
        private ObjectManager $manager,
        private CartChangesResetterInterface $cartChangesResetter,
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(
        Request $request,
        ?string $formType = null,
        ?string $template = null,
        ?string $redirect = null,
    ): Response {
        $template ??= '@SyliusShop/cart/index.html.twig';
        $formType ??= CartType::class;
        $redirect ??= 'sylius_shop_checkout_start';

        $cart = $this->cartContext->getCart();

        $form = $this->formFactory->create($formType, $cart);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $cart = $form->getData();

            $this->eventDispatcher->dispatch(new GenericEvent($cart), SyliusCartEvents::CART_CHANGE);
            $this->manager->flush();

            return new RedirectResponse($this->router->generate($redirect));
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->cartChangesResetter->resetChanges($cart);
            $this->addErrorNotification('sylius.cart.not_recalculated');
        }

        return new Response($this->twig->render($template, [
            'cart' => $cart,
            'form' => $form->createView(),
        ]));
    }

    private function addErrorNotification(string $message): void
    {
        $session = $this->requestStack->getSession();

        Assert::isInstanceOf($session, FlashBagAwareSessionInterface::class);

        $session->getFlashBag()->add('error', $message);
    }
}
