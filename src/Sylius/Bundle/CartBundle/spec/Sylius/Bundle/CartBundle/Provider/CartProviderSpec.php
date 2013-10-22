<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CartBundle\SyliusCartEvents;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartProviderSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CartBundle\Storage\CartStorageInterface      $storage
     * @param Doctrine\Common\Persistence\ObjectManager                  $manager
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface     $repository
     * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    function let($storage, $manager, $repository, $eventDispatcher)
    {
        $this->beConstructedWith($storage, $manager, $repository, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Provider\CartProvider');
    }

    function it_implements_Sylius_cart_provider_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Provider\CartProviderInterface');
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_looks_for_cart_by_identifier_if_any_in_storage($storage, $repository, $eventDispatcher, $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(3);
        $repository->find(3)->shouldBeCalled()->willReturn($cart);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_INITIALIZE, Argument::any())->shouldNotBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_creates_new_cart_if_there_is_no_identifier_in_storage($storage, $repository, $eventDispatcher, $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(null);
        $repository->createNew()->willReturn($cart);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_INITIALIZE, Argument::any())->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_creates_new_cart_if_identifier_is_wrong($storage, $repository, $eventDispatcher, $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(7);
        $repository->find(7)->shouldBeCalled()->willReturn(null);
        $repository->createNew()->willReturn($cart);
        $eventDispatcher->dispatch(SyliusCartEvents::CART_INITIALIZE, Argument::any())->shouldBeCalled();

        $this->getCart()->shouldReturn($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_resets_current_cart_identifier_in_storage_when_abandoning_cart($storage, $repository, $storage, $eventDispatcher, $cart)
    {
        $this->setCart($cart);
        $storage->setCurrentCartIdentifier($cart)->shouldBeCalled();

        $storage->resetCurrentCartIdentifier()->shouldBeCalled();
        $eventDispatcher->dispatch(SyliusCartEvents::CART_ABANDON, Argument::any())->shouldBeCalled();

        $this->abandonCart();
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_sets_current_cart_identifier_when_setting_cart($storage, $cart)
    {
        $storage->setCurrentCartIdentifier($cart)->shouldBeCalled();

        $this->setCart($cart);
    }
}
