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

namespace Sylius\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentMethodController extends ResourceController
{
    public function getPaymentGatewaysAction(Request $request, string $template): Response
    {
        return $this->render(
            $template,
            [
                'gatewayFactories' => $this->getParameter('sylius.gateway_factories'),
                'metadata' => $this->metadata,
            ],
        );
    }
}
