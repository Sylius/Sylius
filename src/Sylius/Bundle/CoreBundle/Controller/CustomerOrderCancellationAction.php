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

use Doctrine\ORM\EntityManagerInterface;
use SM\SMException;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\CoreBundle\Checker\CustomerOrderCancellationCheckerInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class CustomerOrderCancellationAction
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var CustomerOrderCancellationCheckerInterface */
    private $customerOrderCancellationChecker;

    /** @var StateMachineFactoryInterface */
    private $stateMachineFactory;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var Session */
    private $session;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CustomerOrderCancellationCheckerInterface $customerOrderCancellationChecker,
        StateMachineFactoryInterface $stateMachineFactory,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $entityManager,
        Session $session
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerOrderCancellationChecker = $customerOrderCancellationChecker;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    public function __invoke(Request $request): Response
    {
        $orderNumber = $request->attributes->get('orderNumber');
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        if (!$this->customerOrderCancellationChecker->check($order)) {
            $this->session->getFlashBag()->add('error', 'sylius.order.cancel_error');
            return new RedirectResponse($this->urlGenerator->generate('sylius_shop_account_order_index'));
        }

        try {
            $this->stateMachineFactory
                ->get($order, OrderTransitions::GRAPH)
                ->apply(OrderTransitions::TRANSITION_CANCEL)
            ;
        } catch (SMException $e) {
            $this->session->getFlashBag()->add('error', 'sylius.order.cancel_error');
            return new RedirectResponse($this->urlGenerator->generate('sylius_shop_account_order_index'));
        }

        $this->entityManager->flush();

        return new RedirectResponse($this->urlGenerator->generate('sylius_shop_account_order_index'));
    }
}
