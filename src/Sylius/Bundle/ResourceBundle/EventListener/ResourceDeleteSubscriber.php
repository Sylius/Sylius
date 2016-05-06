<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\EventListener;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface as RestViewHandlerInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ResourceDeleteSubscriber implements EventSubscriberInterface
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RestViewHandlerInterface
     */
    private $viewHandler;

    /**
     * @param UrlGeneratorInterface $router
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     * @param RestViewHandlerInterface $viewHandler
     */
    public function __construct(
        UrlGeneratorInterface $router,
        SessionInterface $session,
        TranslatorInterface $translator,
        RestViewHandlerInterface $viewHandler
    ) {
        $this->router = $router;
        $this->session = $session;
        $this->translator = $translator;
        $this->viewHandler = $viewHandler;
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

        $requestAttributes = $event->getRequest()->attributes;
        $originalRoute = $requestAttributes->get('_route');
        $resourceName = $this->getResourceNameFromRoute($originalRoute);

        if (!$this->isHtmlRequest($event->getRequest())) {
            $event->setResponse(
                $this->viewHandler->handle(View::create([
                    'error' => [
                        'code' => $exception->getSQLState(),
                        'message' => $this->translator->trans('sylius.resource.delete_error', ['%resource%' => $resourceName], 'flashes'),
                    ]
                ], 500))
            );

            return;
        }

        if (null === $requestAttributes->get('_controller')) {
            return;
        }

        $this->session->getBag('flashes')->add(
            'error',
            $this->translator->trans('sylius.resource.delete_error', ['%resource%' => $resourceName], 'flashes')
        );

        $referrer = $event->getRequest()->headers->get('referer');

        if ($this->refersFromShow($referrer)) {
            $event->setResponse(
                $this->createRedirectResponse($originalRoute, ResourceActions::SHOW, ['id' => $requestAttributes->get('id')])
            );

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
     * @param string $referrer
     *
     * @return bool
     */
    private function refersFromShow($referrer)
    {
        $referrerArray = explode('/', $referrer);

        return is_numeric(array_pop($referrerArray));
    }

    /**
     * @param string $originalRoute
     * @param string $targetAction
     * @param array $parameters
     *
     * @return RedirectResponse
     */
    private function createRedirectResponse($originalRoute, $targetAction, array $parameters = [])
    {
        $redirectRoute = str_replace(ResourceActions::DELETE, $targetAction, $originalRoute);

        return new RedirectResponse($this->router->generate($redirectRoute, $parameters));
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    private function isHtmlRequest(Request $request)
    {
        return 'html' === $request->getRequestFormat();
    }
}
