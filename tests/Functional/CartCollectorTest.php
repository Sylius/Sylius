<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\Functional;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Sylius\Bundle\CoreBundle\Collector\CartCollector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CartCollectorTest extends KernelTestCase
{
    /** @test */
    public function it_has_no_cart_when_no_channel_is_present(): void
    {
        $this->loadFixtures([]);

        $collector = self::getContainer()->get(CartCollector::class);
        $collector->collect(new Request(), new Response());

        $this->assertFalse($collector->hasCart());
    }

    /** @test */
    public function it_has_no_cart_data_when_no_cart_and_no_session_are_available(): void
    {
        $this->loadFixtures([__DIR__ . '/../DataFixtures/ORM/resources/order.yml']);

        $collector = self::getContainer()->get(CartCollector::class);
        $collector->collect(new Request(), new Response());

        $this->assertFalse($collector->hasCart());
    }

    /** @test */
    public function it_has_no_cart_data_when_cart_is_not_available(): void
    {
        $this->loadFixtures([__DIR__ . '/../DataFixtures/ORM/resources/order.yml']);

        $sessionFactory = self::getContainer()->get('session.factory');
        $session = $sessionFactory->createSession();

        $request = new Request();
        $request->setSession($session);

        $requestStack = self::getContainer()->get('request_stack');
        $requestStack->push($request);

        $collector = self::getContainer()->get(CartCollector::class);
        $collector->collect($request, new Response());

        $this->assertFalse($collector->hasCart());
    }

    /** @test */
    public function it_returns_no_cart_data_when_request_is_stateless(): void
    {
        $sessionFactory = self::getContainer()->get('session.factory');
        $session = $sessionFactory->createSession();

        $request = new Request();
        $request->setSession($session);
        $request->attributes->set('_stateless', true);

        $requestStack = self::getContainer()->get('request_stack');
        $requestStack->push($request);

        $fixtures = $this->loadFixtures([__DIR__ . '/../DataFixtures/ORM/resources/cart.yml']);
        /** @var ChannelInterface $channel */
        $channel = $fixtures['channel_web'];
        /** @var OrderInterface $cart */
        $cart = $fixtures['order_001'];

        $sessionCartStorage = self::getContainer()->get(CartStorageInterface::class);
        $sessionCartStorage->setForChannel($channel, $cart);

        $collector = self::getContainer()->get(CartCollector::class);
        $collector->collect($request, new Response());

        $this->assertFalse($collector->hasCart());
    }

    /** @test */
    public function it_collects_cart_data(): void
    {
        $sessionFactory = self::getContainer()->get('session.factory');
        $session = $sessionFactory->createSession();

        $request = new Request();
        $request->setSession($session);

        $requestStack = self::getContainer()->get('request_stack');
        $requestStack->push($request);

        $fixtures = $this->loadFixtures([__DIR__ . '/../DataFixtures/ORM/resources/cart.yml']);
        /** @var ChannelInterface $channel */
        $channel = $fixtures['channel_web'];
        /** @var OrderInterface $cart */
        $cart = $fixtures['order_001'];

        $sessionCartStorage = self::getContainer()->get(CartStorageInterface::class);
        $sessionCartStorage->setForChannel($channel, $cart);

        /** @var CartCollector $collector */
        $collector = self::getContainer()->get(CartCollector::class);
        $collector->collect($request, new Response());

        $this->assertTrue($collector->hasCart());
        $this->assertSame($cart->getId(), $collector->getId());
        $this->assertSame($cart->getCurrencyCode(), $collector->getCurrency());
        $this->assertSame($cart->getLocaleCode(), $collector->getLocale());
        $this->assertSame($cart->getTotal(), $collector->getTotal());
        $this->assertSame($cart->getItemsTotal(), $collector->getSubtotal());
        $this->assertSame($cart->getItemUnits()->count(), $collector->getQuantity());
        $this->assertSame([
            'main' => $cart->getState(),
            'checkout' => $cart->getCheckoutState(),
            'shipping' => $cart->getShippingState(),
            'payment' => $cart->getPaymentState(),
        ], $collector->getStates());
        $this->assertSame(
            array_map(fn (OrderItemInterface $item) => $item->getId(), $cart->getItems()->toArray()),
            array_column($collector->getItems(), 'id')
        );
    }

    private function loadFixtures(array $fixtureFiles): array
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        return $fixtureLoader->load($fixtureFiles, [], [], PurgeMode::createDeleteMode());
    }
}
