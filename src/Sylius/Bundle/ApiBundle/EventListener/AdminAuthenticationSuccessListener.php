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

namespace Sylius\Bundle\ApiBundle\EventListener;

use ApiPlatform\Core\Api\IriConverterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Sylius\Component\Core\Model\AdminUserInterface;

final class AdminAuthenticationSuccessListener
{
    public function __construct(private IriConverterInterface $iriConverter)
    {
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $adminUser = $event->getUser();

        if (!$adminUser instanceof AdminUserInterface) {
            return;
        }

        $data['adminUser'] = $this->iriConverter->getIriFromItem($adminUser);

        $event->setData($data);
    }
}
