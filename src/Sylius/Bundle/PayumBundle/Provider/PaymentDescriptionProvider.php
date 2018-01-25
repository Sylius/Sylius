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

namespace Sylius\Bundle\PayumBundle\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class PaymentDescriptionProvider implements PaymentDescriptionProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentDescription(PaymentInterface $payment): string
    {
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        return $this->translator->transChoice(
            'sylius.payum_action.payment.description',
            $order->getItems()->count(),
            [
                '%items%' => $order->getItems()->count(),
                '%total%' => round($order->getTotal() / 100, 2),
            ]
        );
    }
}
