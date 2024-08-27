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

use Sylius\Bundle\ResourceBundle\Controller\RequestConfigurationFactoryInterface;
use Sylius\Bundle\ShopBundle\Provider\PayResponseProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class OrderPayController
{
    /**
     * @param iterable<PayResponseProviderInterface> $payResponseProviders
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private MetadataInterface $orderMetadata,
        private RequestConfigurationFactoryInterface $requestConfigurationFactory,
        private iterable $payResponseProviders,
        private PayResponseProviderInterface $defaultPayResponseProvider,
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

        return $this->defaultPayResponseProvider->getResponse($configuration, $order);
    }
}
