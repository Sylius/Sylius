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

namespace spec\Sylius\Bundle\ApiBundle\DataTransformer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\AddProductReview;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class LoggedInShopUserEmailAwareCommandDataTransformerSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext)
    {
        $this->beConstructedWith($userContext);
    }

    function it_adds_email_to_add_product_review_for_logged_in_customer(
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer
    ): void {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);

        $this->transform(
            new AddProductReview(
                'Good stuff',
                5,
                'Really good stuff',
                'winter_cap'
            ),
            'Sylius\Component\Core\Model\ProductReview',
            []
        );
    }

    function it_does_not_add_email_to_add_product_review_for_visitor(
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer
    ): void {
        $userContext->getUser()->willReturn($shopUser);

        $shopUser->getCustomer()->willReturn($customer);

        $this->transform(
            new AddProductReview(
                'Good stuff',
                5,
                'Really good stuff',
                'winter_cap',
                'john@example.com'
            ),
            'Sylius\Component\Core\Model\ProductReview',
            []
        );
    }

    function it_supports_command_for_adding_product_review(): void
    {
        $this->supportsTransformation(
            new AddProductReview(
                'Good stuff',
                5,
                'Really good stuff',
                'winter_cap'
            )
        )->shouldReturn(true);
    }

    function it_supports_only_add_product_review_command(): void
    {
        $this->supportsTransformation(new PickupCart())->shouldReturn(false);
    }
}
