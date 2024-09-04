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

namespace Sylius\Bundle\ShopBundle\Handler;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class PaymentStatusFlashHandler implements PaymentStatusFlashHandlerInterface
{
    public function handle(RequestConfiguration $requestConfiguration, string $status): void
    {
        $request = $requestConfiguration->getRequest();
        if (PaymentInterface::STATE_NEW !== $status) {
            /** @var FlashBagInterface $flashBag */
            $flashBag = $request->getSession()->getBag('flashes');
            $flashBag->add('info', sprintf('sylius.payment.%s', $status));
        }
    }
}
