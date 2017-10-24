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

namespace Sylius\Bundle\CoreBundle\Checkout;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

final class CheckoutRedirectListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var CheckoutStateUrlGeneratorInterface
     */
    private $checkoutStateUrlGenerator;

    /**
     * @var RequestMatcherInterface
     */
    private $requestMatcher;

    /**
     * @param RequestStack $requestStack
     * @param CheckoutStateUrlGeneratorInterface $checkoutStateUrlGenerator
     * @param RequestMatcherInterface $requestMatcher
     */
    public function __construct(
        RequestStack $requestStack,
        CheckoutStateUrlGeneratorInterface $checkoutStateUrlGenerator,
        RequestMatcherInterface $requestMatcher
    ) {
        $this->requestStack = $requestStack;
        $this->checkoutStateUrlGenerator = $checkoutStateUrlGenerator;
        $this->requestMatcher = $requestMatcher;
    }

    /**
     * @param ResourceControllerEvent $resourceControllerEvent
     */
    public function handleCheckoutRedirect(ResourceControllerEvent $resourceControllerEvent): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$this->requestMatcher->matches($request) || isset($request->attributes->get('_sylius')['redirect'])) {
            return;
        }

        $order = $resourceControllerEvent->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $resourceControllerEvent->setResponse(new RedirectResponse($this->checkoutStateUrlGenerator->generateForOrderCheckoutState($order)));
    }
}
