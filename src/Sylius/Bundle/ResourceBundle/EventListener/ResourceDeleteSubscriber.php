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

use Coduo\ToString\StringConverter;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Exception\ResourceConstraintViolationException;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use FOS\RestBundle\View\ViewHandlerInterface as RestViewHandlerInterface;

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
        if (!$exception instanceof ResourceConstraintViolationException) {
            return;
        }

        $requestAttributes = $event->getRequest()->attributes;
        $originalRoute = $requestAttributes->get('_route');

        if (null === $requestAttributes->has('_controller')) {
            return;
        }

        $resource = $exception->getResource();
        $requestConfiguration = $exception->getRequestConfiguration();

        $resourceName = $requestConfiguration->getMetadata()->getName();
        $message = $this->translator->trans(
            sprintf('sylius.resource.%s.delete_error', $resourceName),
            [
                '%resource%' => $this->castResourceToString($resource)
            ],
            'flashes'
        );

        if (!$this->isHtmlRequest($event->getRequest())) {
            $event->setResponse(
                $this->viewHandler->handle(View::create([
                    'error' => [
                        'code' => $exception->getSQLState(),
                        'message' => $message,
                    ]
                ], 409))
            );

            return;
        }

        $this->session->getBag('flashes')->add('error', $message);
        $referrer = $event->getRequest()->headers->get('referer');

        if (null !== $referrer) {
            $event->setResponse(new RedirectResponse($referrer));

            return;
        }

        $event->setResponse($this->createRedirectResponse($originalRoute, ResourceActions::INDEX));
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

    /**
     * @param $resource
     *
     * @return string
     */
    protected function castResourceToString($resource)
    {
        return new StringConverter($resource);
    }
}
