<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\OrderBundle\Templating\Helper\CartHelper;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Templating\Helper\Helper;

final class CartHelperSpec extends ObjectBehavior
{
    function let(
        CartContextInterface $cartContext,
        FactoryInterface $itemFactory,
        FormFactoryInterface $formFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->beConstructedWith($cartContext, $itemFactory, $formFactory, $orderItemQuantityModifier);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CartHelper::class);
    }

    function it_is_a_twig_extension()
    {
        $this->shouldHaveType(Helper::class);
    }

    function its_getCurrentCart_returns_current_cart_via_provider($cartContext, OrderInterface $cart)
    {
        $cartContext->getCart()->willReturn($cart);

        $this->getCurrentCart()->shouldReturn($cart);
    }

    function its_getItemFormView_returns_a_form_view_of_cart_item_form(
        $formFactory,
        $itemFactory,
        $orderItemQuantityModifier,
        OrderItemInterface $item,
        FormInterface $form,
        FormView $formView
    ) {
        $itemFactory->createNew()->shouldBeCalled()->willReturn($item);
        $orderItemQuantityModifier->modify($item, 1)->shouldBeCalled();

        $formFactory->create('sylius_cart_item', $item, [])->shouldBeCalled()->willReturn($form);
        $form->createView()->willReturn($formView);

        $this->getItemFormView()->shouldReturn($formView);
    }

    function its_getItemFormView_uses_given_options_when_creating_form(
        $itemFactory,
        $formFactory,
        $orderItemQuantityModifier,
        FormInterface $form,
        FormView $formView,
        OrderItemInterface $item
    ) {
        $itemFactory->createNew()->shouldBeCalled()->willReturn($item);
        $orderItemQuantityModifier->modify($item, 1)->shouldBeCalled();

        $formFactory->create('sylius_cart_item', $item, ['foo' => 'bar'])->shouldBeCalled()->willReturn($form);
        $form->createView()->willReturn($formView);

        $this->getItemFormView(['foo' => 'bar'])->shouldReturn($formView);
    }
}
