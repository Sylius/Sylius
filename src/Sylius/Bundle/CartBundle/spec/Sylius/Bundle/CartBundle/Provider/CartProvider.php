<?php

namespace spec\Sylius\Bundle\CartBundle\Provider;

use PHPSpec2\ObjectBehavior;

/**
 * Cart provider spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CartProvider extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CartBundle\Storage\CartStorageInterface         $cartStorage
     * @param Sylius\Bundle\ResourceBundle\Manager\ResourceManagerInterface $cartManager
     */
    function let($cartStorage, $cartManager)
    {
        $this->beConstructedWith($cartStorage, $cartManager);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Provider\CartProvider');
    }

    function it_should_reset_current_cart_identifier_when_abandoning_cart($cartStorage)
    {
        $cartStorage->resetCurrentCartIdentifier()->shouldBeCalled();

        $this->abandonCart();
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_set_current_cart_identifier_when_setting_cart($cartStorage, $cart)
    {
        $cartStorage->setCurrentCartIdentifier($cart)->shouldBeCalled();

        $this->setCart($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_look_for_cart_by_identifier_if_any($cartStorage, $cartManager, $cart)
    {
        $cartStorage->getCurrentCartIdentifier()->willReturn(3);
        $cartManager->find(3)->shouldBeCalled()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_create_new_cart_if_there_is_no_identifier_in_storage($cartStorage, $cartManager, $cart)
    {
        $cartStorage->getCurrentCartIdentifier()->willReturn(null);
        $cartManager->create()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_create_new_cart_if_identifier_is_wrong($cartStorage, $cartManager, $cart)
    {
        $cartStorage->getCurrentCartIdentifier()->willReturn(7);
        $cartManager->find(7)->shouldBeCalled()->willReturn(null);
        $cartManager->create()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }
}
