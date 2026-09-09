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

use Doctrine\ORM\EntityManagerInterface;
use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Bundle\ShopBundle\Twig\Component\Cart\FormComponent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\Model\OrderItemUnit;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\SyliusCartEvents;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;

final class CartPostFlushEventsTest extends KernelTestCase
{
    #[Test]
    public function it_passes_the_cart_as_an_argument_of_the_cart_item_post_remove_event(): void
    {
        $cart = $this->createCartWithItem();
        $component = $this->createComponentForCart($cart);

        $capturedSubjectOrder = false;
        $capturedCart = null;

        $this->listenTo(SyliusCartEvents::CART_ITEM_POST_REMOVE, function (GenericEvent $event) use (&$capturedSubjectOrder, &$capturedCart): void {
            $capturedSubjectOrder = $event->getSubject()->getOrder();
            $capturedCart = $event->hasArgument('cart') ? $event->getArgument('cart') : null;
        });

        $component->removeItem(0);

        // the item is detached from its cart by CartItemRemoveListener, so the subject alone is not enough
        $this->assertNull($capturedSubjectOrder);
        $this->assertSame($cart, $capturedCart);
    }

    #[Test]
    public function it_passes_the_cleared_cart_id_as_an_argument_of_the_cart_post_clear_event(): void
    {
        $cart = $this->createCartWithItem();
        $cartId = $cart->getId();
        $component = $this->createComponentForCart($cart);

        $capturedSubjectId = false;
        $capturedCartId = null;

        $this->listenTo(SyliusCartEvents::CART_POST_CLEAR, function (GenericEvent $event) use (&$capturedSubjectId, &$capturedCartId): void {
            $capturedSubjectId = $event->getSubject()->getId();
            $capturedCartId = $event->hasArgument('cartId') ? $event->getArgument('cartId') : null;
        });

        $component->clearCart();

        // Doctrine resets the identifier of a removed entity on flush, so the subject alone is not enough
        $this->assertNull($capturedSubjectId);
        $this->assertSame($cartId, $capturedCartId);
    }

    private function createCartWithItem(): OrderInterface
    {
        $fixtures = $this->loadFixtures([__DIR__ . '/../DataFixtures/ORM/resources/cart.yml']);

        /** @var OrderInterface $cart */
        $cart = $fixtures['order_001'];
        /** @var ProductVariantInterface $variant */
        $variant = $fixtures['mug_sw'];

        $orderItem = new OrderItem();
        $orderItem->setVariant($variant);
        $orderItem->setUnitPrice(2000);
        new OrderItemUnit($orderItem);

        $cart->addItem($orderItem);

        $manager = $this->getManager();
        $manager->persist($cart);
        $manager->flush();

        return $cart;
    }

    private function createComponentForCart(OrderInterface $cart): FormComponent
    {
        // the cart form is CSRF-protected, so building its view requires a request with a session
        $request = new Request();
        $request->setSession(self::getContainer()->get('session.factory')->createSession());
        self::getContainer()->get('request_stack')->push($request);

        /** @var FormComponent $component */
        $component = self::getContainer()->get('sylius_shop.twig.component.cart.form');
        $component->resource = $cart;
        $component->initializeForm([]);

        return $component;
    }

    private function listenTo(string $eventName, callable $listener): void
    {
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = self::getContainer()->get('event_dispatcher');
        $dispatcher->addListener($eventName, $listener);
    }

    private function getManager(): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine')->getManager();
    }

    private function loadFixtures(array $fixtureFiles): array
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        return $fixtureLoader->load($fixtureFiles, [], [], PurgeMode::createDeleteMode());
    }
}
