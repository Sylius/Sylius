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

namespace Sylius\Bundle\CoreBundle\Provider;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class SessionProvider
{
    public static function getSession(RequestStack|SessionInterface $requestStackOrSession): SessionInterface
    {
        if ($requestStackOrSession instanceof SessionInterface) {
            return $requestStackOrSession;
        }

        return $requestStackOrSession->getSession();
    }
}
