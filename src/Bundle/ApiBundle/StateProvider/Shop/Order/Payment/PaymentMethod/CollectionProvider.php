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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Payment\PaymentMethod;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<PaymentMethodInterface> */
final readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private OrderRepositoryInterface $orderRepository,
        private SectionProviderInterface $sectionProvider,
        private PaymentMethodsResolverInterface $paymentMethodsResolver,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|Collection
    {
        Assert::true(is_a($operation->getClass(), PaymentMethodInterface::class, true));
        Assert::isInstanceOf($operation, GetCollection::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);
        Assert::keyExists($context, ContextKeys::CHANNEL);
        Assert::keyExists($uriVariables, 'tokenValue');
        Assert::keyExists($uriVariables, 'paymentId');

        /** @var ChannelInterface $channel */
        $channel = $context[ContextKeys::CHANNEL];

        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findCartByTokenValueAndChannel($uriVariables['tokenValue'], $channel);

        if ($cart === null) {
            return [];
        }

        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->findOneByOrderToken($uriVariables['paymentId'], $cart->getTokenValue());

        if ($payment === null) {
            return [];
        }

        return $this->paymentMethodsResolver->getSupportedMethods($payment);
    }
}
