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

namespace Sylius\Bundle\ApiBundle\Controller\Payum;

use Payum\Core\Payum;
use Sylius\Bundle\PayumBundle\Factory\GetStatusFactoryInterface;
use Sylius\Bundle\PayumBundle\Model\PaymentSecurityTokenInterface;
use Symfony\Component\HttpFoundation\Request;

/** @experimental */
final class AfterPayPayment
{
    public function __construct(
        private Payum $payum,
        private GetStatusFactoryInterface $getStatusFactory,
    ) {
    }

    public function __invoke(Request $request): PaymentSecurityTokenInterface
    {
        /** @var PaymentSecurityTokenInterface $token */
        $token = $this->payum->getHttpRequestVerifier()->verify($request);

        $gateway = $this->payum->getGateway($token->getGatewayName());

        $statusRequest = $this->getStatusFactory->createNewWithModel($token);

        $gateway->execute($statusRequest);

        $this->payum->getHttpRequestVerifier()->invalidate($token);

        return $token;
    }
}
