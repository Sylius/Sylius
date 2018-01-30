<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 29/01/18
 * Time: 18:28
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\EventListener;


use Sylius\Component\Core\URLRedirect\URLRedirectProcessorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RouterListener implements EventSubscriberInterface
{

    /**
     * @var URLRedirectProcessorInterface
     */
    private $redirectService;

    public function __construct(URLRedirectProcessorInterface $redirectService)
    {
        $this->redirectService = $redirectService;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $uri     = $request->getRequestUri();
        $path    = parse_url($uri)['path'];

        $newRoute = $this->redirectService->redirectRoute($path);
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
            KernelEvents::REQUEST => ['onKernelRequest']
        ];
    }
}