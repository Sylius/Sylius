<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Cart;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CartBundle\Model\CartItemInterface;
use Sylius\Bundle\CoreBundle\Checker\RestrictedZoneCheckerInterface;
use Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\CartBundle\Provider\CartProviderInterface;
use Sylius\Bundle\CoreBundle\Calculator\PriceCalculatorInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ItemResolverSpec extends ObjectBehavior
{
    function let(
        CartProviderInterface $cartProvider,
        PriceCalculatorInterface $priceCalculator,
        RepositoryInterface $productRepository,
        FormFactoryInterface $formFactory,
        AvailabilityCheckerInterface $availabilityChecker,
        RestrictedZoneCheckerInterface $restrictedZoneChecker
    )
    {
        $this->beConstructedWith($cartProvider, $priceCalculator, $productRepository, $formFactory, $availabilityChecker, $restrictedZoneChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Cart\ItemResolver');
    }

    function it_implements_Sylius_cart_item_resolver_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CartBundle\Resolver\ItemResolverInterface');
    }

    function it_throws_exception_unless_request_method_is_POST_or_PUT(CartItemInterface $item, Request $request)
    {
        $request->isMethod('POST')->willReturn(false);
        $request->isMethod('PUT')->willReturn(false);

        $this
            ->shouldThrow('Sylius\Bundle\CartBundle\Resolver\ItemResolvingException')
            ->duringResolve($item, $request)
        ;
    }

    function it_throws_exception_when_product_id_is_missing_on_request(CartItemInterface $item, Request $request)
    {
        $request->isMethod('POST')->willReturn(true);
        $request->get('id')->willReturn(null);

        $this
            ->shouldThrow('Sylius\Bundle\CartBundle\Resolver\ItemResolvingException')
            ->duringResolve($item, $request)
        ;
    }

    function it_throws_exception_if_product_with_given_id_does_not_exist($productRepository, CartItemInterface $item, Request $request)
    {
        $request->isMethod('POST')->willReturn(true);
        $request->get('id')->willReturn(5);

        $productRepository->find(5)->willReturn(null);

        $this
            ->shouldThrow('Sylius\Bundle\CartBundle\Resolver\ItemResolvingException')
            ->duringResolve($item, $request)
        ;
    }
}
