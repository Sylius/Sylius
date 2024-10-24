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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\Payment\PaymentRequest;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\PaymentBundle\Checker\FinalizedPaymentRequestCheckerInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProviderInterface<PaymentRequestInterface>
 *
 * @experimental
 */
final readonly class ItemProvider implements ProviderInterface
{
    /** @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository */
    public function __construct(
        private SectionProviderInterface $sectionProvider,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private FinalizedPaymentRequestCheckerInterface $finalizedPaymentRequestChecker,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        Assert::true(is_a($operation->getClass(), PaymentRequestInterface::class, true));
        Assert::isInstanceOf($operation, Put::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);

        $paymentRequest = $this->paymentRequestRepository->find($uriVariables['hash']);

        if (
            $paymentRequest === null ||
            $this->finalizedPaymentRequestChecker->isFinal($paymentRequest)
        ) {
            return null;
        }

        return $paymentRequest;
    }
}
