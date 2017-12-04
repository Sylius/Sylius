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

namespace Sylius\Component\Payment\Resolver;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class PaymentMethodsResolver implements PaymentMethodsResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @param RepositoryInterface $paymentMethodRepository
     */
    public function __construct(RepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedMethods(PaymentInterface $payment): array
    {
        return $this->paymentMethodRepository->findBy(['enabled' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PaymentInterface $payment): bool
    {
        return true;
    }
}
