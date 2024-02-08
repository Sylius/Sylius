<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\DataTransformer;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\CustomerEmailAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class LoggedInCustomerEmailAwareCommandDataTransformerSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext)
    {
        $this->beConstructedWith($userContext);
    }

    function it_adds_email_to_customer_email_aware_commands_from_logged_in_customer(
        UserContextInterface $userContext,
        CustomerEmailAwareInterface $command,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
    ): void {
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $customer->getEmail()->willReturn('sample@email.com');

        $command->setEmail('sample@email.com')->shouldBeCalled();

        $this->transform(
            $command,
            'Sylius\Component\Core\Model\ProductReview',
            [],
        );
    }

    function it_does_not_add_email_to_customer_email_aware_for_visitor(
        UserContextInterface $userContext,
        CustomerEmailAwareInterface $command,
    ): void {
        $userContext->getUser()->willReturn(null);

        $command->setEmail('sample@email.com')->shouldNotBeCalled();

        $this->transform(
            $command,
            'Sylius\Component\Core\Model\ProductReview',
            [],
        );
    }

    function it_supports_command_with_customer_email_aware_interface(CustomerEmailAwareInterface $command): void
    {
        $this->supportsTransformation($command)->shouldReturn(true);
    }
}
