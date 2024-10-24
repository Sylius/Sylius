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

namespace Sylius\Behat\Service\PaymentRequest\Provider;

use Sylius\Bundle\PaymentBundle\Attribute\AsNotifyPaymentProvider;
use Sylius\Bundle\PaymentBundle\Provider\NotifyPaymentProviderInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Symfony\Component\HttpFoundation\Request;

#[AsNotifyPaymentProvider()]
final class DummyNotifyPaymentProvider implements NotifyPaymentProviderInterface
{
    public function __construct(
        private EntityRepository $paymentRepository,
    ) {
    }

    public function getPayment(Request $request, PaymentMethodInterface $paymentMethod): PaymentInterface
    {
        /** @var PaymentInterface[] $payments */
        $payments = $this->paymentRepository->findBy(
            [],
            ['createdAt' => 'ASC'],
            1,
        );

        return $payments[0];
    }

    public function supports(Request $request, PaymentMethodInterface $paymentMethod): bool
    {
        return true;
    }
}
