<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout;

use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CheckoutRedirectHandler implements RedirectHandlerInterface
{
    /**
     * @var RedirectHandlerInterface
     */
    private $decoratedRedirectHandler;

    /**
     * @var CheckoutStateUrlGeneratorInterface
     */
    private $checkoutStateUrlGenerator;

    /**
     * @var RequestMatcherInterface
     */
    private $requestMatcher;

    /**
     * @param RedirectHandlerInterface $decoratedRedirectHandler
     * @param CheckoutStateUrlGeneratorInterface $checkoutStateUrlGenerator
     * @param RequestMatcherInterface $requestMatcher
     */
    public function __construct(
        RedirectHandlerInterface $decoratedRedirectHandler,
        CheckoutStateUrlGeneratorInterface $checkoutStateUrlGenerator,
        RequestMatcherInterface $requestMatcher
    ) {
        $this->decoratedRedirectHandler = $decoratedRedirectHandler;
        $this->checkoutStateUrlGenerator = $checkoutStateUrlGenerator;
        $this->requestMatcher = $requestMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function redirectToResource(RequestConfiguration $configuration, ResourceInterface $resource)
    {
        $request = $configuration->getRequest();
        if (!$this->requestMatcher->matches($request) || isset($request->attributes->get('_sylius')['redirect'])) {
            return $this->decoratedRedirectHandler->redirectToResource($configuration, $resource);
        }

        Assert::isInstanceOf($resource, OrderInterface::class);

        return new RedirectResponse($this->checkoutStateUrlGenerator->generateForOrderCheckoutState($resource));
    }

    /**
     * {@inheritdoc}
     */
    public function redirectToIndex(RequestConfiguration $configuration, ResourceInterface $resource = null)
    {
        return $this->decoratedRedirectHandler->redirectToIndex($configuration, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function redirectToRoute(RequestConfiguration $configuration, $route, array $parameters = [])
    {
        return $this->decoratedRedirectHandler->redirectToRoute($configuration, $route, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function redirect(RequestConfiguration $configuration, $url, $status = 302)
    {
        return $this->redirect($configuration, $url, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function redirectToReferer(RequestConfiguration $configuration)
    {
        return $this->decoratedRedirectHandler->redirectToReferer($configuration);
    }
}
