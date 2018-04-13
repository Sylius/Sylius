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

namespace Sylius\Bundle\AdminApiBundle\Tests\Event;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class GetResponseForExceptionEventPropagationTest extends KernelTestCase
{
    /** @var Container */
    private $container;

    /** @var EventDispatcher */
    private $eventDispatcher;

    public function setUp()
    {
        parent::setUp();
        static::bootKernel();

        $this->container = new ContainerBuilder();
        $this->container->autowire('event_dispatcher', 'Symfony\Component\EventDispatcher\EventDispatcher');

        $this->container->autowire('app.test_exception_listener', 'Sylius\Bundle\AdminApiBundle\Tests\Event\ResourceDeleteSubscriber');

        $this->container
            ->autowire('app.exception_listener', 'Sylius\Bundle\AdminApiBundle\Tests\Event\ResourceDeleteSubscriber')
            ->addTag('kernel.event_subscriber', array('event' => 'kernel.exception'))
            ->addTag('priority', array(-255));
        ;

        $this->eventDispatcher = $this->container->get('event_dispatcher');
    }

    /**
     * @test
     */
    public function get_response_for_exception_event_propagation_stopped_test(): void
    {
        $this->eventDispatcher->dispatch('kernel.exception');

        $testEventListener = $this->container->get('app.test_exception_listener');
        self::assertEquals(0, $testEventListener->getEventsCaught());
    }
}

final class ResourceDeleteSubscriber implements EventSubscriberInterface
{
    private $eventsCaught = 0;

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onResourceDelete',
        ];
    }

    /**
     * @return int
     */
    public function getEventsCaught(): int
    {
        return $this->eventsCaught;
    }

    public function onResourceDelete()
    {
        $this->eventsCaught++;
    }
}