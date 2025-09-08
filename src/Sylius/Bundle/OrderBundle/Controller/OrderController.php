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

use Sylius\Bundle\OrderBundle\Resetter\CartChangesResetterInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Sylius\Resource\ResourceActions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OrderController extends ResourceController
{
    public function summaryAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $cart = $this->getCurrentCart();
        if (null !== $cart->getId()) {
            $cart = $this->getOrderRepository()->findCartById($cart->getId());
        }

        $this->getEventDispatcher()->dispatch(new GenericEvent($cart), SyliusCartEvents::CART_SUMMARY);
        $event = $this->eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $cart);
        $eventResponse = $event->getResponse();
        if (null !== $eventResponse) {
            return $eventResponse;
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->createRestView($configuration, $cart);
        }

        $form = $this->resourceFormFactory->create($configuration, $cart);

        return $this->render(
            $configuration->getTemplate('summary.html'),
            [
                'cart' => $cart,
                'form' => $form->createView(),
            ],
        );
    }

    public function saveAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $resource = $this->getCurrentCart();

        $form = $this->resourceFormFactory->create($configuration, $resource);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isSubmitted() && $form->isValid()) {
            $resource = $form->getData();

            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $resource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                return $this->redirectHandler->redirectToResource($configuration, $resource);
            }

            if ($configuration->hasStateMachine()) {
                $this->stateMachine->apply($configuration, $resource);
            }

            $this->eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $resource);

            $this->getEventDispatcher()->dispatch(new GenericEvent($resource), SyliusCartEvents::CART_CHANGE);
            $this->manager->flush();

            if (!$configuration->isHtmlRequest()) {
                return $this->createRestView($configuration, null, Response::HTTP_NO_CONTENT);
            }

            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource);

            return $this->redirectHandler->redirectToResource($configuration, $resource);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $this->getCartResetter()->resetChanges($resource);
            $this->addFlash('error', 'sylius.cart.not_recalculated');
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->createRestView($configuration, $form, Response::HTTP_BAD_REQUEST);
        }

        return $this->render(
            $configuration->getTemplate(ResourceActions::UPDATE . '.html'),
            [
                'configuration' => $configuration,
                $this->metadata->getName() => $resource,
                'form' => $form->createView(),
                'cart' => $resource,
            ],
        );
    }

    protected function addFlash(string $type, mixed $message): void
    {
        /** @var SessionInterface $session */
        $session = $this->get('request_stack')->getSession();
        /** @var FlashBagInterface $flashBag */
        $flashBag = $session->getBag('flashes');
        $flashBag->add($type, $message);
    }

    protected function getCurrentCart(): OrderInterface
    {
        return $this->getContext()->getCart();
    }

    protected function getContext(): CartContextInterface
    {
        return $this->get('sylius.context.cart');
    }

    protected function getOrderRepository(): OrderRepositoryInterface
    {
        return $this->get('sylius.repository.order');
    }

    protected function getCartResetter(): CartChangesResetterInterface
    {
        return $this->get('sylius.resetter.cart_changes');
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->container->get('event_dispatcher');
    }
}
