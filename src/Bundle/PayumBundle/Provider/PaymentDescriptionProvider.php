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

namespace Sylius\Bundle\PayumBundle\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PaymentDescriptionProvider implements PaymentDescriptionProviderInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function getPaymentDescription(PaymentInterface $payment): string
    {
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        return $this->translator->trans(
            'sylius.payum_action.payment.description',
            [
                '%items%' => $order->getItems()->count(),
                '%total%' => round($payment->getAmount() / 100, 2),
            ],
        );
    }
}
