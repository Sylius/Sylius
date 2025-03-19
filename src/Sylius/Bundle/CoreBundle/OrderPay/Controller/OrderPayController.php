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

namespace Sylius\Bundle\CoreBundle\OrderPay\Controller;

use LogicException;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\AfterPayResponseProviderInterface;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\PayResponseProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** @experimental */
final class OrderPayController
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     * @param iterable<PayResponseProviderInterface> $payResponseProviders
     * @param iterable<AfterPayResponseProviderInterface> $afterPayResponseProviders
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private MetadataInterface $orderMetadata,
        private RequestConfigurationFactoryInterface $requestConfigurationFactory,
        private iterable $payResponseProviders,
        private iterable $afterPayResponseProviders,
    ) {
    }

    public function payAction(Request $request, string $tokenValue): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->orderMetadata, $request);

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByTokenValue($tokenValue);

        if (null === $order) {
            throw new NotFoundHttpException(sprintf('Order with token "%s" does not exist.', $tokenValue));
        }

        $request->getSession()->set('sylius_order_id', $order->getId());

        foreach ($this->payResponseProviders as $provider) {
            if ($provider->supports($configuration, $order)) {
                return $provider->getResponse($configuration, $order);
            }
        }

        throw new LogicException(sprintf('No "pay response provider" available for order (id %s).', $order->getId()));
    }

    public function afterPayAction(Request $request): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->orderMetadata, $request);

        foreach ($this->afterPayResponseProviders as $provider) {
            if ($provider->supports($configuration)) {
                return $provider->getResponse($configuration);
            }
        }

        throw new LogicException('No "after pay response provider" available.');
    }
}
