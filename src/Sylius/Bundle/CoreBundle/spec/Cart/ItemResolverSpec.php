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
use Sylius\Component\Addressing\Checker\RestrictedZoneCheckerInterface;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ItemResolverSpec extends ObjectBehavior
{
    function let(
        CartProviderInterface $cartProvider,
        RepositoryInterface $productRepository,
        FormFactoryInterface $formFactory,
        AvailabilityCheckerInterface $availabilityChecker,
        RestrictedZoneCheckerInterface $restrictedZoneChecker,
        DelegatingCalculatorInterface $priceCalculator,
        ChannelContextInterface $channelContext
    ) {
        $this->beConstructedWith(
            $cartProvider,
            $productRepository,
            $formFactory,
            $availabilityChecker,
            $restrictedZoneChecker,
            $priceCalculator,
            $channelContext
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Cart\ItemResolver');
    }

    function it_implements_Sylius_cart_item_resolver_interface()
    {
        $this->shouldImplement('Sylius\Component\Cart\Resolver\ItemResolverInterface');
    }

    function it_throws_exception_unless_request_method_is_POST_or_PUT(CartItemInterface $item, Request $request)
    {
        $request->isMethod('POST')->willReturn(false);
        $request->isMethod('PUT')->willReturn(false);

        $this
            ->shouldThrow('Sylius\Component\Cart\Resolver\ItemResolvingException')
            ->duringResolve($item, $request)
        ;
    }

    function it_throws_exception_when_product_id_is_missing_on_request(CartItemInterface $item, Request $request)
    {
        $request->isMethod('POST')->willReturn(true);
        $request->get('id')->willReturn(null);

        $this
            ->shouldThrow('Sylius\Component\Cart\Resolver\ItemResolvingException')
            ->duringResolve($item, $request)
        ;
    }

    function it_throws_exception_if_product_with_given_id_does_not_exist(
        $productRepository,
        CartItemInterface $item,
        Request $request
    ) {
        $request->isMethod('POST')->willReturn(true);
        $request->get('id')->willReturn(5);

        $productRepository->findOneBy(array('id' => 5, 'channels' => null))->willReturn(null);

        $this
            ->shouldThrow('Sylius\Component\Cart\Resolver\ItemResolvingException')
            ->duringResolve($item, $request)
        ;
    }
}
