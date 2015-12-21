<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CartBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Templating\Helper\Helper;

class CartHelperSpec extends ObjectBehavior
{
    function let(
        CartProviderInterface $cartProvider,
        FactoryInterface $itemFactory,
        FormFactoryInterface $formFactory
    ) {
        $this->beConstructedWith($cartProvider, $itemFactory, $formFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CartBundle\Templating\Helper\CartHelper');
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType(Helper::class);
    }

    function its_getCurrentCart_returns_current_cart_via_provider($cartProvider, CartInterface $cart)
    {
        $cartProvider->getCart()->willReturn($cart);

        $this->getCurrentCart()->shouldReturn($cart);
    }

    function its_getItemFormView_returns_a_form_view_of_cart_item_form(
        $itemFactory,
        $formFactory,
        FormInterface $form,
        FormView $formView,
        CartItemInterface $item
    ) {
        $itemFactory->createNew()->shouldBeCalled()->willReturn($item);
        $formFactory->create('sylius_cart_item', $item, array())->shouldBeCalled()->willReturn($form);
        $form->createView()->willReturn($formView);

        $this->getItemFormView()->shouldReturn($formView);
    }

    function its_getItemFormView_uses_given_options_when_creating_form(
        $itemFactory,
        $formFactory,
        FormInterface $form,
        FormView $formView,
        CartItemInterface $item
    ) {
        $itemFactory->createNew()->shouldBeCalled()->willReturn($item);
        $formFactory->create('sylius_cart_item', $item, array('foo' => 'bar'))->shouldBeCalled()->willReturn($form);
        $form->createView()->willReturn($formView);

        $this->getItemFormView(array('foo' => 'bar'))->shouldReturn($formView);
    }
}
