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

namespace Sylius\Bundle\ApiBundle\Controller\Payment;

use Sylius\Bundle\ApiBundle\Provider\PaymentConfigurationProvider;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class GetPaymentConfiguration
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var PaymentConfigurationProvider */
    private $paymentConfigurationProvider;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        PaymentConfigurationProvider $paymentConfigurationProvider
    ) {
        $this->orderRepository = $orderRepository;
        $this->paymentConfigurationProvider = $paymentConfigurationProvider;
    }

    public function __invoke(Request $request): Response
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->find($request->get('id'));

        return $this->paymentConfigurationProvider->provide($order->getLastPayment());
    }
}
