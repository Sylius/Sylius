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

namespace Sylius\Component\Payment\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of PaymentRequestInterface
 *
 * @extends RepositoryInterface<T>
 *
 * @experimental
 */
interface PaymentRequestRepositoryInterface extends RepositoryInterface
{
    public function findOneByPaymentId(mixed $hash, mixed $paymentId): ?PaymentRequestInterface;

    public function createQueryBuilderForPayment(string $paymentId): QueryBuilder;

    public function duplicateExists(PaymentRequestInterface $paymentRequest): bool;

    /**
     * @param array<string> $states
     *
     * @return array<PaymentRequestInterface>
     */
    public function findByPaymentIdAndStates(mixed $paymentId, array $states): array;

    public function findOneByActionPaymentAndMethod(
        string $action,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
    ): ?PaymentRequestInterface;
}
