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

namespace Sylius\Bundle\ShopBundle\Provider\Offline;

use Sylius\Bundle\CoreBundle\PaymentRequest\Provider\HttpResponseProviderInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

final class StatusHttpResponseProvider implements HttpResponseProviderInterface
{
    /**
     * @param array<string, string> $finalRouteParameters
     */
    public function __construct(
        private RouterInterface $router,
        private string $finalRoute,
        private array $finalRouteParameters,
    ) {
    }

    public function supports(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): bool {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_STATUS;
    }

    public function getResponse(
        RequestConfiguration $requestConfiguration,
        PaymentRequestInterface $paymentRequest,
    ): Response {
        $finalUrl = $this->router->generate($this->finalRoute, $this->finalRouteParameters);

        return new RedirectResponse($finalUrl);
    }
}
