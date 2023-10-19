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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Input;

use ApiPlatform\Metadata\Operation;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LoggedInCustomerEmailIfNotSetAwareInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class LoggedInCustomerEmailIfNotSetAwareInputDataProcessorSpec extends ObjectBehavior
{
    function let(UserContextInterface $userContext)
    {
        $this->beConstructedWith($userContext);
    }

    function it_adds_email_to_contact_aware_command_from_logged_in_customer_if_its_not_set(
        UserContextInterface $userContext,
        LoggedInCustomerEmailIfNotSetAwareInterface $command,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        Operation $operation,
    ): void {
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $command->getEmail()->willReturn(null);
        $customer->getEmail()->willReturn('sample@email.com');
        $command->setEmail('sample@email.com')->shouldBeCalled();

        $this->process($command, $operation)->shouldReturn([$command, $operation, [], []]);
    }

    function it_does_not_add_email_to_contact_aware_command_if_provided(
        UserContextInterface $userContext,
        LoggedInCustomerEmailIfNotSetAwareInterface $command,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        Operation $operation,
    ): void {
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);

        $command->getEmail()->willReturn('sample@email.com');
        $customer->getEmail()->shouldNotBeCalled();
        $command->setEmail('sample@email.com')->shouldNotBeCalled();

        $this->process($command, $operation)->shouldReturn([$command, $operation, [], []]);
    }

    function it_early_returns_contact_aware_command_if_admin_user_provided(
        UserContextInterface $userContext,
        LoggedInCustomerEmailIfNotSetAwareInterface $command,
        AdminUserInterface $adminUser,
        Operation $operation,
    ): void {
        $userContext->getUser()->willReturn($adminUser);

        $command->getEmail()->shouldNotBeCalled();

        $this->process($command, $operation)->shouldReturn([$command, $operation, [], []]);
    }

    function it_adds_nothing_for_visitor(
        UserContextInterface $userContext,
        LoggedInCustomerEmailIfNotSetAwareInterface $command,
        Operation $operation,
    ): void {
        $userContext->getUser()->willReturn(null);

        $command->setEmail('sample@email.com')->shouldNotBeCalled();

        $this->process($command, $operation)->shouldReturn([$command, $operation, [], []]);
    }

    function it_can_process_command_with_contact_aware_interface(
        LoggedInCustomerEmailIfNotSetAwareInterface $command,
        LocaleCodeAwareInterface $localeCommand,
        Operation $operation,
    ): void {
        $this->supports($command, $operation)->shouldReturn(true);
        $this->supports($localeCommand, $operation)->shouldReturn(false);
    }
}
