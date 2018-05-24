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

use SM\SMException;
use Sylius\Bundle\CoreBundle\Checker\CustomerOrderCancellationCheckerInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class OrderCancellationController
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CustomerOrderCancellationCheckerInterface */
    private $customerOrderCancellationChecker;

    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerOrderCancellationCheckerInterface $customerOrderCancellationChecker,
        StateMachineFactoryInterface $stateMachineFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerOrderCancellationChecker = $customerOrderCancellationChecker;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public function cancelAction(Request $request): Response
    {
        $order = $this->orderRepository->findOneByNumber($request->attributes->get('orderNumber'));

        if (!$this->customerOrderCancellationChecker->check($order)) {
            return null;
        }

        try {
            $this->stateMachineFactory
                ->get($order, OrderTransitions::GRAPH)
                ->apply(OrderTransitions::TRANSITION_CANCEL);
        } catch (SMException $e) {
            return null;
        }

        return new RedirectResponse($this->urlGenerator->generate('sylius_shop_order_show'));
    }

}
