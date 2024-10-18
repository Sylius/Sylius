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

namespace Sylius\Bundle\CoreBundle\OrderPay\Handler;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

/** @experimental */
final class PaymentStateFlashHandler implements PaymentStateFlashHandlerInterface
{
    public function __construct(private string $format = 'sylius.payment.%s')
    {
    }

    public function handle(RequestConfiguration $requestConfiguration, string $state): void
    {
        $request = $requestConfiguration->getRequest();

        if (!$request->hasSession()) {
            return;
        }

        /** @var FlashBagAwareSessionInterface $session */
        $session = $request->getSession();
        if (PaymentInterface::STATE_NEW !== $state) {
            $session->getFlashBag()->add('info', sprintf($this->format, $state));
        }
    }
}
