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

namespace Sylius\Bundle\CoreBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentMethodController extends ResourceController
{
    public function getPaymentGatewaysAction(Request $request, string $template): Response
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $view = View::create()
            ->setTemplate($template)
            ->setTemplateVar($this->metadata->getPluralName())
            ->setData([
                'gatewayFactories' => $this->getParameter('sylius.gateway_factories'),
                'metadata' => $this->metadata,
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }
}
