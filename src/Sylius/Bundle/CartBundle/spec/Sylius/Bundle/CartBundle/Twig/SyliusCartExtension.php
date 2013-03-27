<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Twig;

use PHPSpec2\ObjectBehavior;

/**
 * Sylius cart extensions for Twig spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusCartExtension extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CartBundle\Provider\CartProviderInterface $cartProvider
     * @param Doctrine\Common\Persistence\ObjectRepository            $itemRepository
     * @param Symfony\Component\Form\FormFactory                      $formFactory
     */
    function let($cartProvider, $itemRepository, $formFactory)
    {
        $this->beConstructedWith($cartProvider, $itemRepository, $formFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Twig\SyliusCartExtension');
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    /**
     * @param Sylius\Bundle\CartBundle\Model\CartInterface $cart
     */
    function its_getCurrentCart_returns_current_cart_via_provider($cartProvider, $cart)
    {
        $cartProvider->getCart()->willReturn($cart);

        $this->getCurrentCart()->shouldReturn($cart);
    }

    /**
     * @param Symfony\Component\Form\Form                      $form
     * @param Symfony\Component\Form\FormViewInterface         $formView
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item
     */
    function its_getItemFormView_returns_a_form_view_of_cart_item_form(
        $itemRepository, $formFactory, $form, $formView, $item
    )
    {
        $itemRepository->createNew()->shouldBeCalled()->willReturn($item);
        $formFactory->create('sylius_cart_item', $item, array())->shouldBeCalled()->willReturn($form);
        $form->createView()->willReturn($formView);

        $this->getItemFormView()->shouldReturn($formView);
    }

    /**
     * @param Symfony\Component\Form\Form                      $form
     * @param Symfony\Component\Form\FormViewInterface         $formView
     * @param Sylius\Bundle\CartBundle\Model\CartItemInterface $item
     */
    function its_getItemFormView_uses_given_options_when_creating_form(
        $itemRepository, $formFactory, $form, $formView, $item
    )
    {
        $itemRepository->createNew()->shouldBeCalled()->willReturn($item);
        $formFactory->create('sylius_cart_item', $item, array('foo' => 'bar'))->shouldBeCalled()->willReturn($form);
        $form->createView()->willReturn($formView);

        $this->getItemFormView(array('foo' => 'bar'))->shouldReturn($formView);
    }
}
