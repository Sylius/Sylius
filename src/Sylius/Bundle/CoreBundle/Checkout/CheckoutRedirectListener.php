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

namespace Sylius\Bundle\CoreBundle\Checkout;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class CheckoutRedirectListener
{
    public function __construct(
        private RequestStack $requestStack,
        private CheckoutStateUrlGeneratorInterface $checkoutStateUrlGenerator,
        private RequestMatcherInterface $requestMatcher,
    ) {
    }

    public function handleCheckoutRedirect(ResourceControllerEvent $resourceControllerEvent): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (
            null === $request ||
            !$this->requestMatcher->matches($request) ||
            isset($request->attributes->get('_sylius', [])['redirect'])
        ) {
            return;
        }

        $order = $resourceControllerEvent->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $resourceControllerEvent->setResponse(
            new RedirectResponse($this->checkoutStateUrlGenerator->generateForOrderCheckoutState($order)),
        );
    }
}
