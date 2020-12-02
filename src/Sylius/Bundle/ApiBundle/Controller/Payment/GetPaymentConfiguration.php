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

use Sylius\Bundle\ApiBundle\Provider\PaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

/** @experimental */
final class GetPaymentConfiguration
{
    /** @var PaymentRepositoryInterface */
    private $paymentRepository;

    /** @var PaymentConfigurationProviderInterface */
    private $paymentConfigurationProvider;

    /** @var OrderRepositoryInterface $orderRepository */
    private $orderRepository;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        PaymentConfigurationProviderInterface $paymentConfigurationProvider,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->paymentConfigurationProvider = $paymentConfigurationProvider;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(Request $request): JsonResponse
    {
        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->find($request->get('paymentId'));

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByTokenValue($request->get('id'));

        Assert::notNull($order);
        Assert::notNull($payment);

        Assert::same($payment->getOrder(), $order);

        return new JsonResponse($this->paymentConfigurationProvider->provide($payment));
    }
}
