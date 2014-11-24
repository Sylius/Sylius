<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customer\EventListener;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class CustomerListener
{
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $email = $event->getRequest()->attributes->get('customer');
        if (!$email) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->setCookie(new Cookie('_sylius[customer]', $email, new \DateTime('+1 month')));

        $event->setResponse($response);
    }
} 