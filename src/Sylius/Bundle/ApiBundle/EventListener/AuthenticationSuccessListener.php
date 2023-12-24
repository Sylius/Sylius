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

namespace Sylius\Bundle\ApiBundle\EventListener;

use ApiPlatform\Api\IriConverterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class AuthenticationSuccessListener
{
    public function __construct(private IriConverterInterface $iriConverter)
    {
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof ShopUserInterface) {
            return;
        }

        /** @var CustomerInterface $customer */
        $customer = $user->getCustomer();

        $data['customer'] = $this->iriConverter->getIriFromResource($customer);

        $event->setData($data);
    }
}
