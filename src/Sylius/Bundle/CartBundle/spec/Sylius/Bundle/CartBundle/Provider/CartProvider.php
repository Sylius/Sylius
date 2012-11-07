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
     * @param Sylius\Bundle\CartBundle\Storage\CartStorageInterface               $storage
     * @param Sylius\Bundle\ResourceBundle\Manager\ResourceManagerInterface       $manager
     * @param Sylius\Bundle\ResourceBundle\Repository\ResourceRepositoryInterface $repository
     */
    function let($storage, $manager, $repository)
    {
        $this->beConstructedWith($storage, $manager, $repository);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Provider\CartProvider');
    }

    function it_should_reset_current_cart_identifier_when_abandoning_cart($storage)
    {
        $storage->resetCurrentCartIdentifier()->shouldBeCalled();

        $this->abandonCart();
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_set_current_cart_identifier_when_setting_cart($storage, $cart)
    {
        $storage->setCurrentCartIdentifier($cart)->shouldBeCalled();

        $this->setCart($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_look_for_cart_by_identifier_if_any($storage, $repository, $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(3);
        $repository->get(array('id' => 3))->shouldBeCalled()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_create_new_cart_if_there_is_no_identifier_in_storage($storage, $manager, $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(null);
        $manager->create()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function it_should_create_new_cart_if_identifier_is_wrong($storage, $manager, $repository, $cart)
    {
        $storage->getCurrentCartIdentifier()->willReturn(7);
        $repository->get(array('id' => 7))->shouldBeCalled()->willReturn(null);
        $manager->create()->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }
}
