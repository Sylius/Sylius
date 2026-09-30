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

namespace Sylius\Bundle\CoreBundle\OAuth\EventListener;

use Sylius\Bundle\CoreBundle\OAuth\Exception\UnverifiedEmailException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

final readonly class UnverifiedEmailLoginFailureListener
{
    public function __invoke(LoginFailureEvent $event): void
    {
        if (!$event->getException() instanceof UnverifiedEmailException) {
            return;
        }

        $request = $event->getRequest();
        if (!$request->hasSession()) {
            return;
        }

        $request->getSession()->set(SecurityRequestAttributes::AUTHENTICATION_ERROR, $event->getException());
    }
}
