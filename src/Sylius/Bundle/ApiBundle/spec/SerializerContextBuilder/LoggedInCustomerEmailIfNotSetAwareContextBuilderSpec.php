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

namespace spec\Sylius\Bundle\ApiBundle\SerializerContextBuilder;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

final class LoggedInCustomerEmailIfNotSetAwareContextBuilderSpec extends ObjectBehavior
{
    function let(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        UserContextInterface $userContext,
    ): void {
        $this->beConstructedWith($decoratedContextBuilder, $userContext);
    }

    function it_does_not_add_email_to_contact_aware_command_if_provided(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        UserContextInterface $userContext,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]])
        ;

        $request->toArray()->willReturn([
            'email' => 'email@example.com',
            'message' => 'message',
        ]);
        $userContext->getUser()->shouldNotBeCalled();

        $this->createFromRequest($request, true, [])->shouldReturn(['input' => ['class' => SendContactRequest::class]]);
    }

    function it_early_returns_contact_aware_command_if_admin_user_provided(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        UserContextInterface $userContext,
        AdminUserInterface $adminUser,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]])
        ;

        $request->toArray()->willReturn(['message' => 'message']);
        $userContext->getUser()->willReturn($adminUser);

        $this->createFromRequest($request, true, [])->shouldReturn(['input' => ['class' => SendContactRequest::class]]);
    }

    function it_adds_nothing_for_visitor(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        UserContextInterface $userContext,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]])
        ;

        $request->toArray()->willReturn(['message' => 'message']);
        $userContext->getUser()->willReturn(null);

        $this->createFromRequest($request, true, [])->shouldReturn(['input' => ['class' => SendContactRequest::class]]);
    }

    function it_adds_email_if_not_provided_and_the_user_is_logged_in(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        UserContextInterface $userContext,
        ShopUserInterface $shopUser,
        CustomerInterface $customer,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => SendContactRequest::class]])
        ;

        $request->toArray()->willReturn(['message' => 'message']);
        $userContext->getUser()->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('email@example.com');

        $this
            ->createFromRequest($request, true, [])
            ->shouldReturn([
                'input' => ['class' => SendContactRequest::class],
                AbstractNormalizer::DEFAULT_CONSTRUCTOR_ARGUMENTS => [
                    SendContactRequest::class => ['email' => 'email@example.com'],
                ],
            ])
        ;
    }

    function it_works_only_for_logged_in_customer_email_if_not_set_interface(
        SerializerContextBuilderInterface $decoratedContextBuilder,
        Request $request,
    ): void {
        $decoratedContextBuilder
            ->createFromRequest($request, true, [])
            ->willReturn(['input' => ['class' => \stdClass::class]])
        ;

        $request->toArray()->shouldNotBeCalled();

        $this->createFromRequest($request, true, [])->shouldReturn(['input' => ['class' => \stdClass::class]]);
    }
}
