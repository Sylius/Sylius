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

namespace Sylius\Bundle\CoreBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\OrderBundle\Controller\OrderController as BaseOrderController;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

class OrderController extends BaseOrderController
{
    public function summaryAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $cart = $this->getCurrentCart();
        if (null !== $cart->getId()) {
            $orderRepository = $this->getOrderRepository();

            Assert::isInstanceOf($orderRepository, OrderRepositoryInterface::class);

            $cart = $orderRepository->findCartForSummary($cart->getId());
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($cart));
        }

        $form = $this->resourceFormFactory->create($configuration, $cart);

        $view = View::create()
            ->setTemplate($configuration->getTemplate('summary.html'))
            ->setData([
                'cart' => $cart,
                'form' => $form->createView(),
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    public function thankYouAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $orderId = $request->getSession()->get('sylius_order_id', null);

        if (null === $orderId) {
            $options = $configuration->getParameters()->get('after_failure');

            return $this->redirectHandler->redirectToRoute(
                $configuration,
                $options['route'] ?? 'sylius_shop_homepage',
                $options['parameters'] ?? []
            );
        }

        $request->getSession()->remove('sylius_order_id');
        $order = $this->repository->find($orderId);
        Assert::notNull($order);

        $view = View::create()
            ->setData([
                'order' => $order,
            ])
            ->setTemplate($configuration->getParameters()->get('template'))
        ;

        return $this->viewHandler->handle($configuration, $view);
    }
}
