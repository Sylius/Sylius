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

namespace Sylius\Bundle\ShopBundle\Provider;

use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Response;

final class PaymentRequestPayResponseProvider implements PayResponseProviderInterface
{
    public function getResponse(
        RequestConfiguration $requestConfiguration,
        OrderInterface $order
    ): Response {
        return new Response('', Response::HTTP_NO_CONTENT);
    }
    public function supports(
        RequestConfiguration $requestConfiguration,
        OrderInterface $order
    ): bool {
        return true;
    }
}
