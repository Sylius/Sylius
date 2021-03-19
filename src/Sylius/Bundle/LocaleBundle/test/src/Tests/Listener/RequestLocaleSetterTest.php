<?php

declare(strict_types=1);

namespace Sylius\Bundle\LocaleBundle\test\src\Tests\Listener;

use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestLocaleSetterTest extends WebTestCase
{
    /**
     * @test
     */
    public function it_uses_locale_contexts_at_the_right_moment_to_set_locale_on_request(): void
    {
        /** @var ContainerBuilder $container */
        $container = self::createClient()->getContainer();

        $kernel = $container->get('http_kernel');
        $eventDispatcher = $container->get('event_dispatcher');
        /** @var RequestStack $requestStack */
        $requestStack = $container->get('request_stack');
        $translator = $container->get('translator');

        // request
        $request = new Request();
        $request->setLocale('en_US');
        $request->setDefaultLocale('en_US');

        $requestStack->push($request);

        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST);
        $eventDispatcher->dispatch($event, KernelEvents::REQUEST);

        Assert::assertSame('de_DE', $translator->getLocale());
    }
}
