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

namespace Sylius\Bundle\OrderBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Sylius\Bundle\OrderBundle\Factory\AddToCartCommandFactoryInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Order\CartActions;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Webmozart\Assert\Assert;

class OrderItemController extends ResourceController
{
    public function addAction(Request $request): Response
    {
        $cart = $this->getCurrentCart();
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, CartActions::ADD);
        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->newResourceFactory->create($configuration, $this->factory);

        $this->getQuantityModifier()->modify($orderItem, 1);

        /** @var class-string<FormTypeInterface>|null $formType */
        $formType = $configuration->getFormType();
        Assert::classExists($formType);
        $form = $this->getFormFactory()->create(
            $formType,
            $this->createAddToCartCommand($cart, $orderItem),
            $configuration->getFormOptions(),
        );

        if ($request->isMethod('POST') && $form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            /** @var AddToCartCommandInterface $addToCartCommand */
            $addToCartCommand = $form->getData();
            [$cart, $orderItem] = [$addToCartCommand->getCart(), $addToCartCommand->getCartItem()];

            $errors = $this->getCartItemErrors($orderItem);
            if (0 < count($errors)) {
                $form = $this->getAddToCartFormWithErrors($errors, $form);

                return $this->handleBadAjaxRequestView($configuration, $form);
            }

            $event = $this->eventDispatcher->dispatchPreEvent(CartActions::ADD, $configuration, $orderItem);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                return $this->redirectHandler->redirectToIndex($configuration, $orderItem);
            }

            $this->getOrderModifier()->addToOrder($cart, $orderItem);

            $cartManager = $this->getCartManager();
            $cartManager->persist($cart);
            $cartManager->flush();

            $orderItem = $this->resolveAddedOrderItem($cart, $orderItem);

            $resourceControllerEvent = $this->eventDispatcher->dispatchPostEvent(CartActions::ADD, $configuration, $orderItem);
            if ($resourceControllerEvent->hasResponse()) {
                return $resourceControllerEvent->getResponse();
            }

            $this->flashHelper->addSuccessFlash($configuration, CartActions::ADD, $orderItem);

            if ($request->isXmlHttpRequest()) {
                return $this->viewHandler->handle($configuration, View::create([], Response::HTTP_CREATED));
            }

            return $this->redirectHandler->redirectToResource($configuration, $orderItem);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->handleBadAjaxRequestView($configuration, $form);
        }

        return $this->render(
            $configuration->getTemplate(CartActions::ADD . '.html'),
            [
                'configuration' => $configuration,
                $this->metadata->getName() => $orderItem,
                'form' => $form->createView(),
            ],
        );
    }

    public function removeAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, CartActions::REMOVE);
        /** @var OrderItemInterface $orderItem */
        $orderItem = $this->findOr404($configuration);

        $event = $this->eventDispatcher->dispatchPreEvent(CartActions::REMOVE, $configuration, $orderItem);

        if ($configuration->isCsrfProtectionEnabled() && !$this->isCsrfTokenValid((string) $orderItem->getId(), (string) $request->request->get('_csrf_token'))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        if ($event->isStopped() && !$configuration->isHtmlRequest()) {
            throw new HttpException($event->getErrorCode(), $event->getMessage());
        }
        if ($event->isStopped()) {
            $this->flashHelper->addFlashFromEvent($configuration, $event);

            return $this->redirectHandler->redirectToIndex($configuration, $orderItem);
        }

        $cart = $this->getCurrentCart();
        if ($cart !== $orderItem->getOrder()) {
            $translator = $this->get('translator');
            Assert::isInstanceOf($translator, TranslatorInterface::class);
            $this->addFlash('error', $translator->trans('sylius.cart.cannot_modify', [], 'flashes'));

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
            }

            return $this->redirectHandler->redirectToIndex($configuration, $orderItem);
        }

        $this->getOrderModifier()->removeFromOrder($cart, $orderItem);

        $this->repository->remove($orderItem);

        $cartManager = $this->getCartManager();
        $cartManager->persist($cart);
        $cartManager->flush();

        $this->eventDispatcher->dispatchPostEvent(CartActions::REMOVE, $configuration, $orderItem);

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        $this->flashHelper->addSuccessFlash($configuration, CartActions::REMOVE, $orderItem);

        return $this->redirectHandler->redirectToIndex($configuration, $orderItem);
    }

    protected function getOrderRepository(): OrderRepositoryInterface
    {
        $orderRepository = $this->get('sylius.repository.order');
        Assert::isInstanceOf($orderRepository, OrderRepositoryInterface::class);

        return $orderRepository;
    }

    protected function redirectToCartSummary(RequestConfiguration $configuration): Response
    {
        if (null === $configuration->getParameters()->get('redirect')) {
            return $this->redirectHandler->redirectToRoute($configuration, $this->getCartSummaryRoute());
        }

        return $this->redirectHandler->redirectToRoute($configuration, $configuration->getParameters()->get('redirect'));
    }

    protected function getCartSummaryRoute(): string
    {
        return 'sylius_cart_summary';
    }

    protected function getCurrentCart(): OrderInterface
    {
        return $this->getContext()->getCart();
    }

    protected function getContext(): CartContextInterface
    {
        $cartContext = $this->get('sylius.context.cart');
        Assert::isInstanceOf($cartContext, CartContextInterface::class);

        return $cartContext;
    }

    protected function createAddToCartCommand(OrderInterface $cart, OrderItemInterface $cartItem): AddToCartCommandInterface
    {
        $addToCartCommandFactory = $this->get('sylius.factory.add_to_cart_command');
        Assert::isInstanceOf($addToCartCommandFactory, AddToCartCommandFactoryInterface::class);

        return $addToCartCommandFactory->createWithCartAndCartItem($cart, $cartItem);
    }

    protected function getFormFactory(): FormFactoryInterface
    {
        $formFactory = $this->get('form.factory');
        Assert::isInstanceOf($formFactory, FormFactoryInterface::class);

        return $formFactory;
    }

    protected function getQuantityModifier(): OrderItemQuantityModifierInterface
    {
        $quantityModifier = $this->get('sylius.order_item_quantity_modifier');
        Assert::isInstanceOf($quantityModifier, OrderItemQuantityModifierInterface::class);

        return $quantityModifier;
    }

    protected function getOrderModifier(): OrderModifierInterface
    {
        $orderModifier = $this->get('sylius.order_modifier');
        Assert::isInstanceOf($orderModifier, OrderModifierInterface::class);

        return $orderModifier;
    }

    protected function getCartManager(): EntityManagerInterface
    {
        $cartManager = $this->get('sylius.manager.order');
        Assert::isInstanceOf($cartManager, EntityManagerInterface::class);

        return $cartManager;
    }

    protected function getCartItemErrors(OrderItemInterface $orderItem): ConstraintViolationListInterface
    {
        $validator = $this->get('validator');
        Assert::isInstanceOf($validator, ValidatorInterface::class);

        return $validator
            ->validate($orderItem, null, $this->getParameter('sylius.form.type.order_item.validation_groups'))
        ;
    }

    protected function getAddToCartFormWithErrors(ConstraintViolationListInterface $errors, FormInterface $form): FormInterface
    {
        foreach ($errors as $error) {
            $formSelected = empty($error->getPropertyPath())
                ? $form->get('cartItem')
                : $form->get('cartItem')->get($error->getPropertyPath());

            $formSelected->addError(new FormError((string) $error->getMessage()));
        }

        return $form;
    }

    protected function handleBadAjaxRequestView(RequestConfiguration $configuration, FormInterface $form): Response
    {
        return $this->viewHandler->handle(
            $configuration,
            View::create($form, Response::HTTP_BAD_REQUEST)->setData(['errors' => $form->getErrors(true, true)]),
        );
    }

    protected function resolveAddedOrderItem(OrderInterface $order, OrderItemInterface $item): OrderItemInterface
    {
        return $order->getItems()->filter(fn (OrderItemInterface $orderItem): bool => $orderItem->equals($item))->first();
    }
}
