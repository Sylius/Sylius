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

namespace spec\Sylius\Bundle\CoreBundle\Storage;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Storage\CartStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartSessionStorageSpec extends ObjectBehavior
{
    function let(SessionInterface $session): void
    {
        $this->beConstructedWith($session, 'session_key_name');
    }

    function it_implements_a_cart_storage_interface(): void
    {
        $this->shouldImplement(CartStorageInterface::class);
    }

    function it_sets_a_cart_id_in_a_session(SessionInterface $session): void
    {
        $session->set('session_key_name.channel_code', 14)->shouldBeCalled();

        $this->setCartId('channel_code', 14);
    }

    function it_returns_a_cart_id_from_a_session(SessionInterface $session): void
    {
        $session->get('session_key_name.channel_code')->shouldBeCalled();

        $this->getCartId('channel_code');
    }

    function it_removes_a_cart_from_a_session(SessionInterface $session): void
    {
        $session->remove('session_key_name.channel_code')->shouldBeCalled();

        $this->removeCartId('channel_code');
    }
}
