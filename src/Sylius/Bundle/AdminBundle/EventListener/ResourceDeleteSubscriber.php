<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminBundle\EventListener;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ResourceDeleteSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param UrlGeneratorInterface $router
     * @param SessionInterface $session
     */
    public function __construct(UrlGeneratorInterface $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onResourceDelete',
        ];
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onResourceDelete(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!$exception instanceof ForeignKeyConstraintViolationException) {
            return;
        }

        if (!$event->isMasterRequest() || 'html' !== $event->getRequest()->getRequestFormat()) {
            return;
        }

        $eventRequest = $event->getRequest();
        $requestAttributes = $eventRequest->attributes;
        $originalRoute = $requestAttributes->get('_route');

        if (!$this->isMethodDelete($eventRequest) ||
            !$this->isSyliusRoute($originalRoute) ||
            !$this->isAdminSection($requestAttributes->get('_sylius', []))
        ) {
            return;
        }

        $resourceName = $this->getResourceNameFromRoute($originalRoute);

        if (null === $requestAttributes->get('_controller')) {
            return;
        }

        $this->session->getBag('flashes')->add('error', [
            'message' => 'sylius.resource.delete_error',
            'parameters' => ['%resource%' => $resourceName],
        ]);

        $referrer = $eventRequest->headers->get('referer');
        if (null !== $referrer) {
            $event->setResponse(new RedirectResponse($referrer));

            return;
        }

        $event->setResponse($this->createRedirectResponse($originalRoute, ResourceActions::INDEX));
    }

    /**
     * @param string $route
     *
     * @return string
     */
    private function getResourceNameFromRoute($route)
    {
        $routeArray = explode('_', $route);
        $routeArrayWithoutAction = array_slice($routeArray, 0, count($routeArray) - 1);
        $routeArrayWithoutPrefixes = array_slice($routeArrayWithoutAction, 2);

        return trim(implode(' ', $routeArrayWithoutPrefixes));
    }

    /**
     * @param string $originalRoute
     * @param string $targetAction
     *
     * @return RedirectResponse
     */
    private function createRedirectResponse($originalRoute, $targetAction)
    {
        $redirectRoute = str_replace(ResourceActions::DELETE, $targetAction, $originalRoute);

        return new RedirectResponse($this->router->generate($redirectRoute));
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function isMethodDelete(Request $request)
    {
        return Request::METHOD_DELETE === $request->getMethod();
    }

    /**
     * @param string $route
     *
     * @return bool
     */
    private function isSyliusRoute($route)
    {
        return 0 === strpos($route, 'sylius');
    }

    /**
     * @param array $syliusParameters
     *
     * @return bool
     */
    private function isAdminSection(array $syliusParameters)
    {
        return array_key_exists('section', $syliusParameters) && 'admin' === $syliusParameters['section'];
    }
}
