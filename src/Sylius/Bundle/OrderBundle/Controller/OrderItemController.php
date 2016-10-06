<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Order\CartActions;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderItemController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        if (null === $orderId = $this->getRequest()->get('orderId')) {
            throw new NotFoundHttpException('No order id given.');
        }

        if (!$order = $this->getOrderRepository()->find($orderId)) {
            throw new NotFoundHttpException('Requested order does not exist.');
        }

        $orderItem = parent::createNew();
        $orderItem->setOrder($order);

        return $orderItem;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function addAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::CREATE);
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $this->getItemQuantityModifier()->modify($newResource, 1);

        $form = $this->resourceFormFactory->create($configuration, $newResource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $newResource = $form->getData();

            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::CREATE, $configuration, $newResource);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                return $this->redirectHandler->redirectToIndex($configuration, $newResource);
            }

            $cart = $this->getCurrentCart();
            $this->getOrderModifier()->addToOrder($cart, $newResource);

            $cartManager = $this->getCartManager();
            $cartManager->persist($cart);
            $cartManager->flush();

            $this->eventDispatcher->dispatchPostEvent(ResourceActions::CREATE, $configuration, $newResource);

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create($newResource, Response::HTTP_CREATED));
            }
            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::CREATE, $newResource);

            return $this->redirectHandler->redirectToResource($configuration, $newResource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        $view = View::create()
            ->setData([
                'configuration' => $configuration,
                $this->metadata->getName() => $newResource,
                'form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate(CartActions::ADD . '.html'))
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function removeAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::DELETE);
        $resource = $this->findOr404($configuration);

        $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::DELETE, $configuration, $resource);

        if ($event->isStopped() && !$configuration->isHtmlRequest()) {
            throw new HttpException($event->getErrorCode(), $event->getMessage());
        }
        if ($event->isStopped()) {
            $this->flashHelper->addFlashFromEvent($configuration, $event);

            return $this->redirectHandler->redirectToIndex($configuration, $resource);
        }

        $cart = $this->getCurrentCart();
        $this->getOrderModifier()->removeFromOrder($cart, $resource);

        $this->repository->remove($resource);

        $cartManager = $this->getCartManager();
        $cartManager->persist($cart);
        $cartManager->flush();

        $this->eventDispatcher->dispatchPostEvent(ResourceActions::DELETE, $configuration, $resource);

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
        }

        $this->flashHelper->addSuccessFlash($configuration, ResourceActions::DELETE, $resource);

        return $this->redirectHandler->redirectToIndex($configuration, $resource);
    }

    /**
     * @return ObjectRepository
     */
    protected function getOrderRepository()
    {
        return $this->get('sylius.repository.order');
    }

    /**
     * @param RequestConfiguration $configuration
     *
     * @return RedirectResponse
     */
    protected function redirectToCartSummary(RequestConfiguration $configuration)
    {
        if (null === $configuration->getParameters()->get('redirect')) {
            return $this->redirectHandler->redirectToRoute($configuration, $this->getCartSummaryRoute());
        }

        return $this->redirectHandler->redirectToRoute($configuration, $configuration->getParameters()->get('redirect'));
    }

    /**
     * @return string
     */
    protected function getCartSummaryRoute()
    {
        return 'sylius_cart_summary';
    }

    /**
     * @return OrderInterface
     */
    protected function getCurrentCart()
    {
        return $this->getContext()->getCart();
    }

    /**
     * @return CartContextInterface
     */
    protected function getContext()
    {
        return $this->get('sylius.context.cart');
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher()
    {
        return $this->get('event_dispatcher');
    }

    /**
     * @return OrderItemQuantityModifierInterface
     */
    private function getItemQuantityModifier()
    {
        return $this->get('sylius.order_item_quantity_modifier');
    }

    /**
     * @return OrderModifierInterface
     */
    private function getOrderModifier()
    {
        return $this->get('sylius.cart.order_modifier');
    }

    /**
     * @return EntityManagerInterface
     */
    private function getCartManager()
    {
        return $this->get('sylius.manager.order');
    }
}
