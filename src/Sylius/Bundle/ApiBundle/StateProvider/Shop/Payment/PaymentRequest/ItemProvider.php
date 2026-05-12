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
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Bundle\PaymentBundle\Checker\FinalizedPaymentRequestCheckerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
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
        private ?UserContextInterface $userContext = null,
    ) {
        if ($userContext === null) {
            trigger_deprecation(
                'sylius/api-bundle',
                '2.2',
                'Not passing a "%s" to the "%s" constructor is deprecated and will be required in Sylius 3.0.',
                UserContextInterface::class,
                self::class,
            );
        }
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        Assert::true(is_a($operation->getClass(), PaymentRequestInterface::class, true));
        Assert::isInstanceOf($operation, Put::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);

        $paymentRequest = $this->paymentRequestRepository->find($uriVariables['hash']);
        if (null === $paymentRequest) {
            return null;
        }

        if ($this->userContext !== null && !$this->isAccessibleByCurrentUser($paymentRequest)) {
            return null;
        }

        if ($this->finalizedPaymentRequestChecker->isFinal($paymentRequest)) {
            return null;
        }

        return $paymentRequest;
    }

    private function isAccessibleByCurrentUser(PaymentRequestInterface $paymentRequest): bool
    {
        $user = $this->userContext->getUser();

        if ($user instanceof ShopUserInterface) {
            $customer = $user->getCustomer();

            return $customer instanceof CustomerInterface && $this->isOwnedByCustomer($paymentRequest, $customer);
        }

        return $this->isGuestOrder($paymentRequest);
    }

    private function isOwnedByCustomer(PaymentRequestInterface $paymentRequest, CustomerInterface $customer): bool
    {
        $payment = $paymentRequest->getPayment();
        if (!$payment instanceof PaymentInterface) {
            return false;
        }

        $order = $payment->getOrder();

        return $order instanceof OrderInterface && $order->getCustomer() === $customer;
    }

    private function isGuestOrder(PaymentRequestInterface $paymentRequest): bool
    {
        $payment = $paymentRequest->getPayment();
        if (!$payment instanceof PaymentInterface) {
            return false;
        }

        $order = $payment->getOrder();
        if (!$order instanceof OrderInterface) {
            return false;
        }

        $customer = $order->getCustomer();

        return null === $customer || null === $customer->getUser();
    }
}
