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

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @experimental
 */
final readonly class OrderThankYouAction
{
    private const ORDER_ID_PARAM = 'sylius_order_id';

    public function __construct(
        private Environment $twig,
        private OrderRepositoryInterface $orderRepository,
        private RouterInterface $router,
    ) {
    }

    public function __invoke(Request $request, ?string $template = null): Response
    {
        $template ??= '@SyliusShop/order/thank_you.html.twig';

        $orderId = $request->getSession()->get(self::ORDER_ID_PARAM, null);

        if (null === $orderId) {
            return new RedirectResponse($this->router->generate('sylius_shop_homepage'));
        }

        $request->getSession()->remove(self::ORDER_ID_PARAM);
        $order = $this->orderRepository->findOrderById($orderId);

        if (null === $order) {
            return new RedirectResponse($this->router->generate('sylius_shop_homepage'));
        }

        return new Response($this->twig->render($template, [
            'order' => $order,
        ]));
    }
}
