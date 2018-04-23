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

use Doctrine\DBAL\Driver\DriverException;
use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteSubscriber;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;

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

        $this->container->setParameter('resource', null);

        $this->container->autowire('file_locator', FileLocator::class);
        $this->container->autowire('xml_file_loader', XmlFileLoader::class);
        $this->container->autowire('event_dispatcher', EventDispatcher::class);
        $this->container->autowire('app.url_generator', Router::class)->setArgument(1, null);
        $this->container->setDefinition('session', new Definition(Session::class, [new MockArraySessionStorage()]));
        $this->container
            ->autowire('app.exception_listener', ResourceDeleteSubscriber::class)
            ->addTag('kernel.event_subscriber', array('event' => 'kernel.exception'))
            ->addTag('priority', array(255));
        $this->container
            ->autowire('app.test_exception_listener', TestResourceDeleteSubscriber::class)
            ->addTag('kernel.event_subscriber', array('event' => 'kernel.exception'))
            ->addTag('priority', array(-255));
        ;

        $this->container->compile();
        $this->eventDispatcher = $this->container->get('event_dispatcher');
        $this->eventDispatcher->addSubscriber($this->container->get('app.exception_listener'));
        $this->eventDispatcher->addSubscriber($this->container->get('app.test_exception_listener'));
    }

    /**
     * @test
     */
    public function get_response_for_exception_event_propagation_stopped_test(): void
    {
        $request = new Request();
        $request->setFormat('html', '');
        $request->setMethod(Request::METHOD_DELETE);
        $request->attributes = new ParameterBag(['_route' => 'sylius', '_controller' => '', '_sylius' => ['section' => 'admin']]);

        $event = new GetResponseForExceptionEvent(
            new \AppKernel("test", 0),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new ForeignKeyConstraintViolationException(
                "",
                new PDOException(new \PDOException())))
        ;
        $request->headers->set('referer', 'localhost');

        $this->eventDispatcher->dispatch('kernel.exception', $event);

        $testEventListener = $this->container->get('app.test_exception_listener');
        self::assertEquals(0, $testEventListener->getEventsCaught());
    }
}

final class TestResourceDeleteSubscriber implements EventSubscriberInterface
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