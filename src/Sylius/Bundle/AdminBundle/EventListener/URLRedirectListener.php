<?php
declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\EventListener;

use Sylius\Component\Core\Model\URLRedirect;
use Sylius\Component\Core\URLRedirect\URLRedirectProcessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class URLRedirectListener
 *
 * This class processes the URLRedirects from the database
 *
 * @package Sylius\Bundle\AdminBundle\EventListener
 * @see     URLRedirect
 */
final class URLRedirectListener implements EventSubscriberInterface
{
    /**
     * @var URLRedirectProcessorInterface
     */
    private $redirectProcessor;

    public function __construct(URLRedirectProcessorInterface $redirectProcessor)
    {
        $this->redirectProcessor = $redirectProcessor;
    }

    /**
     * Method that gets triggered on a new event
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $uri     = $request->getRequestUri();
        $path    = rtrim(parse_url($uri)['path'], '/');

        $newRoute = $this->redirectProcessor->redirectRoute($path);
        if ($newRoute !== $path) {
            $event->setResponse(new RedirectResponse($newRoute));
        }
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 64],
        ];
    }
}
